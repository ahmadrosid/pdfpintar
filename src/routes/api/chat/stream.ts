import { createFileRoute } from '@tanstack/react-router'
import { eq } from 'drizzle-orm'

import { getDb } from '#/db/index'
import { documents, messages, threads } from '#/db/schema'
import { auth } from '#/lib/auth/auth'
import { getOwnedDocument } from '#/lib/db/documents'
import { createDocumentAssistant } from '#/lib/openai/assistants'
import {
  formatSseEvent,
  stripCitationMarkers,
} from '#/lib/openai/chat-events'
import {
  getOpenAIClient,
  isPendingOpenAIFileId,
  uploadPdfBytesToOpenAI,
} from '#/lib/openai/client'
import { generateThreadTitle } from '#/lib/openai/title-generator'
import { getPrivatePdfBytes } from '#/lib/storage/s3'

type StreamRequest = {
  text: string
  documentId: number
  threadId?: number
}

const encoder = new TextEncoder()

function sseResponse(stream: ReadableStream<Uint8Array>) {
  return new Response(stream, {
    status: 200,
    headers: {
      'Cache-Control': 'no-cache',
      'Content-Type': 'text/event-stream',
      'X-Accel-Buffering': 'no',
    },
  })
}

export const Route = createFileRoute('/api/chat/stream')({
  server: {
    handlers: {
      POST: async ({ request }) => {
        const session = await auth.api.getSession({ headers: request.headers })
        if (!session) {
          return new Response('Unauthorized', { status: 401 })
        }

        const userId = Number(session.user.id)
        if (!Number.isFinite(userId)) {
          return new Response('Unauthorized', { status: 401 })
        }

        let payload: StreamRequest
        try {
          payload = (await request.json()) as StreamRequest
        } catch {
          return new Response('Invalid JSON', { status: 400 })
        }

        if (!payload.text?.trim() || !Number.isFinite(payload.documentId)) {
          return new Response('Invalid payload', { status: 400 })
        }

        const document = await getOwnedDocument(
          getDb(),
          payload.documentId,
          userId,
        )

        if (!document) {
          return new Response('Unauthorized', { status: 401 })
        }

        const stream = new ReadableStream<Uint8Array>({
          async start(controller) {
            const enqueue = (chunk: string) => {
              controller.enqueue(encoder.encode(chunk))
            }

            try {
              const openai = getOpenAIClient()
              const userMessage = payload.text.trim()
              let openaiFileId = document.fileId

              if (isPendingOpenAIFileId(openaiFileId)) {
                if (!document.filePath) {
                  throw new Error('Document file is missing from storage')
                }

                const pdfBytes = await getPrivatePdfBytes(document.filePath)
                const openaiFile = await uploadPdfBytesToOpenAI(
                  pdfBytes,
                  document.fileName,
                )
                openaiFileId = openaiFile.id

                await getDb()
                  .update(documents)
                  .set({ fileId: openaiFileId })
                  .where(eq(documents.id, document.id))
              }

              let thread:
                | (typeof threads.$inferSelect & { id: number })
                | undefined

              if (!payload.threadId) {
                const openaiThread = await openai.beta.threads.create({
                  messages: [{ role: 'user', content: userMessage }],
                })
                const assistant = await createDocumentAssistant(openai, {
                  fileName: document.fileName,
                  fileId: openaiFileId,
                })

                const [createdThread] = await getDb()
                  .insert(threads)
                  .values({
                    openaiThreadId: openaiThread.id,
                    assistantId: assistant.id,
                    documentId: document.id,
                    title: 'Untitled',
                  })
                  .returning()

                thread = createdThread
                enqueue(
                  formatSseEvent('thread', {
                    id: createdThread.id,
                    openaiThreadId: createdThread.openaiThreadId,
                    assistantId: createdThread.assistantId,
                    documentId: createdThread.documentId,
                    title: createdThread.title,
                  }),
                )
              } else {
                const [existingThread] = await getDb()
                  .select()
                  .from(threads)
                  .where(eq(threads.id, payload.threadId))
                  .limit(1)

                if (!existingThread || existingThread.documentId !== document.id) {
                  enqueue(
                    formatSseEvent('notice', {
                      level: 'error',
                      message: 'Thread not found',
                    }),
                  )
                  controller.close()
                  return
                }

                thread = existingThread
                await openai.beta.threads.messages.create(thread.openaiThreadId, {
                  role: 'user',
                  content: userMessage,
                })
              }

              const run = openai.beta.threads.runs.stream(thread.openaiThreadId, {
                assistant_id: thread.assistantId,
              })

              let messageContent = ''

              for await (const event of run) {
                if (event.event !== 'thread.message.delta') continue

                for (const part of event.data.delta.content ?? []) {
                  if (part.type !== 'text' || !part.text?.value) continue
                  const text = stripCitationMarkers(part.text.value)
                  if (!text) continue
                  messageContent += text
                  enqueue(formatSseEvent('token', text))
                }
              }

              await getDb().insert(messages).values([
                {
                  threadId: thread.id,
                  role: 'user',
                  content: userMessage,
                },
                {
                  threadId: thread.id,
                  role: 'assistant',
                  content: messageContent,
                },
              ])

              if (thread.title === 'Untitled') {
                const title = await generateThreadTitle([
                  { role: 'user', content: userMessage },
                  { role: 'assistant', content: messageContent },
                ])

                const [updatedThread] = await getDb()
                  .update(threads)
                  .set({ title })
                  .where(eq(threads.id, thread.id))
                  .returning()

                enqueue(
                  formatSseEvent('thread', {
                    id: updatedThread.id,
                    openaiThreadId: updatedThread.openaiThreadId,
                    assistantId: updatedThread.assistantId,
                    documentId: updatedThread.documentId,
                    title: updatedThread.title,
                  }),
                )
              }
            } catch (error) {
              console.error(error)
              enqueue(
                formatSseEvent('notice', {
                  level: 'error',
                  message: 'Unexpected error',
                }),
              )
            } finally {
              controller.close()
            }
          },
        })

        return sseResponse(stream)
      },
    },
  },
})
