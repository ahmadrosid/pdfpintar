import { asc, desc, eq } from 'drizzle-orm'
import type { NodePgDatabase } from 'drizzle-orm/node-postgres'

import * as schema from '#/db/schema'
import { documents, messages, threads } from '#/db/schema'
import { getOwnedDocument } from '#/lib/db/documents'

type Db = NodePgDatabase<typeof schema>

export type ChatMessage = {
  id: number
  role: 'user' | 'assistant'
  content: string
}

export type ChatThread = {
  id: number
  openaiThreadId: string
  assistantId: string
  documentId: number
  title: string
}

export type DocumentPageData = {
  document: {
    id: number
    fileName: string
    fileId: string
    isPublic: boolean
    sharingToken: string | null
  }
  thread: ChatThread | null
  messages: ChatMessage[]
}

export type LoadDocumentPageResult =
  | { ok: true; data: DocumentPageData }
  | { ok: false; error: 'not_found' | 'forbidden' }

export async function loadDocumentPageData(
  db: Db,
  documentId: number,
  userId: number,
): Promise<LoadDocumentPageResult> {
  const document = await getOwnedDocument(db, documentId, userId)

  if (!document) {
    const [existing] = await db
      .select({ id: documents.id })
      .from(documents)
      .where(eq(documents.id, documentId))
      .limit(1)

    if (!existing) {
      return { ok: false, error: 'not_found' }
    }

    return { ok: false, error: 'forbidden' }
  }

  const [thread] = await db
    .select()
    .from(threads)
    .where(eq(threads.documentId, document.id))
    .orderBy(desc(threads.createdAt))
    .limit(1)

  const messageRows = thread
    ? await db
        .select()
        .from(messages)
        .where(eq(messages.threadId, thread.id))
        .orderBy(asc(messages.id))
    : []

  return {
    ok: true,
    data: {
      document: {
        id: document.id,
        fileName: document.fileName,
        fileId: document.fileId,
        isPublic: document.isPublic,
        sharingToken: document.sharingToken,
      },
      thread: thread
        ? {
            id: thread.id,
            openaiThreadId: thread.openaiThreadId,
            assistantId: thread.assistantId,
            documentId: thread.documentId,
            title: thread.title,
          }
        : null,
      messages: messageRows.map((message) => ({
        id: message.id,
        role: message.role,
        content: message.content,
      })),
    },
  }
}
