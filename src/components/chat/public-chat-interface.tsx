import { Link, useRouter } from '@tanstack/react-router'
import { useState } from 'react'

import { MessageItem } from '#/components/chat/message-item'
import type { ChatMessage } from '#/lib/documents/page-data'
import { useI18n } from '#/lib/i18n'
import { Route as rootRoute } from '#/routes/__root'
import { copyDocument } from '#/server/documents'

type PublicChatInterfaceProps = {
  documentId: number
  documentName: string
  ownerUserId: number
  messages: ChatMessage[]
}

export function PublicChatInterface({
  documentId,
  documentName,
  ownerUserId,
  messages,
}: PublicChatInterfaceProps) {
  const { t } = useI18n()
  const router = useRouter()
  const { session } = rootRoute.useRouteContext()
  const [copying, setCopying] = useState(false)

  const sessionUserId = session ? Number(session.user.id) : null
  const isOwner = sessionUserId === ownerUserId

  async function handleCopy() {
    setCopying(true)
    try {
      const result = await copyDocument({ data: { documentId } })
      await router.navigate({
        to: '/documents/$documentId',
        params: { documentId: String(result.documentId) },
      })
    } finally {
      setCopying(false)
    }
  }

  return (
    <div className="relative isolate flex h-full max-h-[92vh] flex-col overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-950">
      <div className="border-b border-neutral-200 p-2 dark:border-neutral-800">
        <p className="px-1 text-sm font-medium text-neutral-500 dark:text-neutral-400">
          {t('Shared Chat')}
        </p>
        <p className="px-1 text-sm font-semibold text-neutral-900 dark:text-neutral-100">
          {documentName}
        </p>
      </div>

      <div className="flex-1 overflow-y-auto">
        {messages.length === 0 ? (
          <div className="flex h-full items-center justify-center p-6 text-center text-sm text-neutral-500 dark:text-neutral-400">
            {t('No messages in this chat.')}
          </div>
        ) : (
          messages.map((message, index) => (
            <MessageItem
              key={`${message.role}-${index}`}
              message={{
                role: message.role,
                content: message.content,
              }}
            />
          ))
        )}
      </div>

      {!session ? (
        <div className="border-t border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-950">
          <div className="mx-auto max-w-lg text-center">
            <h3 className="mb-2 text-lg font-semibold text-neutral-900 dark:text-neutral-50">
              {t('Want to try PDFPintar for yourself?')}
            </h3>
            <p className="mb-4 text-sm text-neutral-500 dark:text-neutral-400">
              {t('Sign up now to chat with your own PDF documents using AI.')}
            </p>
            <div className="flex justify-center gap-3">
              <Link to="/register" className="demo-button">
                {t('Sign up for free')}
              </Link>
              <Link to="/login" className="nav-link">
                {t('Log in')}
              </Link>
            </div>
          </div>
        </div>
      ) : null}

      {session && !isOwner ? (
        <div className="border-t border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-950">
          <div className="mx-auto max-w-lg text-center">
            <h3 className="mb-2 text-lg font-semibold text-neutral-900 dark:text-neutral-50">
              {t('Want to chat with this document?')}
            </h3>
            <p className="mb-4 text-sm text-neutral-500 dark:text-neutral-400">
              {t('Copy this document to your library and start your own conversation.')}
            </p>
            <button
              type="button"
              className="demo-button"
              disabled={copying}
              onClick={handleCopy}
            >
              {copying ? t('Copying...') : t('Copy to My Library')}
            </button>
          </div>
        </div>
      ) : null}

      {session && isOwner ? (
        <div className="border-t border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-950">
          <div className="mx-auto max-w-lg text-center">
            <h3 className="mb-2 text-lg font-semibold text-neutral-900 dark:text-neutral-50">
              {t('This is your document')}
            </h3>
            <p className="mb-4 text-sm text-neutral-500 dark:text-neutral-400">
              {t('Continue to your document to start chatting.')}
            </p>
            <Link
              to="/documents/$documentId"
              params={{ documentId: String(documentId) }}
              className="demo-button inline-flex"
            >
              {t('Continue chat with Document')}
            </Link>
          </div>
        </div>
      ) : null}
    </div>
  )
}
