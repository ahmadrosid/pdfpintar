import { Trash2 } from 'lucide-react'
import { useEffect, useRef, useState } from 'react'
import { useRouter } from '@tanstack/react-router'

import { ChatInput } from '#/components/chat/chat-input'
import { MessageItem, type ChatMessageView } from '#/components/chat/message-item'
import type { ChatThread } from '#/lib/documents/page-data'
import { useI18n } from '#/lib/i18n'
import { clearDocumentChat } from '#/server/documents'

type ChatPageProps = {
  documentId: number
  initialMessages: ChatMessageView[]
  initialThread: ChatThread | null
}

type StreamEvent =
  | { type: 'thread'; data: ChatThread }
  | { type: 'token'; data: string }
  | { type: 'notice'; data: { level: 'error'; message: string } }

async function* readSseStream(response: Response) {
  const reader = response.body?.getReader()
  if (!reader) return

  const decoder = new TextDecoder()
  let buffer = ''

  while (true) {
    const { done, value } = await reader.read()
    if (done) break

    buffer += decoder.decode(value, { stream: true })
    const parts = buffer.split('\n\n')
    buffer = parts.pop() ?? ''

    for (const part of parts) {
      const line = part.split('\n').find((entry) => entry.startsWith('data: '))
      if (!line) continue
      yield JSON.parse(line.slice(6)) as StreamEvent
    }
  }
}

export function ChatPage({
  documentId,
  initialMessages,
  initialThread,
}: ChatPageProps) {
  const { t } = useI18n()
  const router = useRouter()
  const scrollRef = useRef<HTMLDivElement>(null)
  const [text, setText] = useState('')
  const [messages, setMessages] = useState(initialMessages)
  const [thread, setThread] = useState(initialThread)
  const [isLoading, setIsLoading] = useState(false)
  const [waitingAssistantResponse, setWaitingAssistantResponse] = useState(false)
  const [notice, setNotice] = useState<string | null>(null)

  useEffect(() => {
    setMessages(initialMessages)
    setThread(initialThread)
  }, [initialMessages, initialThread])

  useEffect(() => {
    const node = scrollRef.current
    if (!node) return
    node.scrollTop = node.scrollHeight
  }, [messages, waitingAssistantResponse])

  async function sendMessage() {
    const trimmed = text.trim()
    if (!trimmed || isLoading) return

    setNotice(null)
    setMessages((current) => [...current, { role: 'user', content: trimmed }])
    setText('')
    setIsLoading(true)
    setWaitingAssistantResponse(true)

    try {
      const response = await fetch('/api/chat/stream', {
        method: 'POST',
        headers: { 'content-type': 'application/json' },
        body: JSON.stringify({
          documentId,
          text: trimmed,
          threadId: thread?.id,
        }),
      })

      if (!response.ok) {
        throw new Error('stream_failed')
      }

      for await (const event of readSseStream(response)) {
        if (event.type === 'thread') {
          setThread(event.data)
          continue
        }

        if (event.type === 'notice') {
          setNotice(event.data.message)
          continue
        }

        if (event.type === 'token') {
          setWaitingAssistantResponse(false)
          setMessages((current) => {
            const last = current[current.length - 1]
            if (last?.role === 'assistant') {
              return [
                ...current.slice(0, -1),
                { ...last, content: last.content + event.data },
              ]
            }

            return [...current, { role: 'assistant', content: event.data }]
          })
        }
      }

      await router.invalidate()
    } catch {
      setNotice(t('Chat failed'))
    } finally {
      setIsLoading(false)
      setWaitingAssistantResponse(false)
    }
  }

  async function handleClearMessages() {
    await clearDocumentChat({ data: { documentId } })
    setMessages([])
    setThread(null)
    await router.invalidate()
  }

  return (
    <div className="relative isolate flex h-full max-h-[92vh] flex-col overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-950">
      <div className="sticky top-0 z-10 flex items-center justify-between border-b border-neutral-200 bg-white p-2 dark:border-neutral-800 dark:bg-neutral-950">
        <p className="px-1 text-sm font-medium text-neutral-900 dark:text-neutral-100">
          {thread?.title ?? t('Untitled')}
        </p>
        <button
          type="button"
          onClick={handleClearMessages}
          className="flex items-center gap-2 p-1 text-sm opacity-50 hover:opacity-100"
        >
          <Trash2 className="h-3 w-3" />
          {t('Delete')}
        </button>
      </div>

      <div
        ref={scrollRef}
        className="scrollbar-thin flex-1 overflow-y-auto"
      >
        {messages.map((message, index) => (
          <MessageItem key={`${message.role}-${index}`} message={message} />
        ))}
        {waitingAssistantResponse ? (
          <div className="flex items-center p-4">
            <p className="animate-pulse text-sm font-medium text-orange-500">
              {t('Thinking...')}
            </p>
          </div>
        ) : null}
        {notice ? (
          <p className="px-4 pb-4 text-sm text-red-600" role="alert">
            {notice}
          </p>
        ) : null}
      </div>

      <ChatInput
        text={text}
        isLoading={isLoading}
        onTextChange={setText}
        onSend={sendMessage}
      />
    </div>
  )
}
