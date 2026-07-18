import { Copy, CopyCheck } from 'lucide-react'
import { useState } from 'react'

import { Markdown } from '#/components/chat/markdown'
import { useI18n } from '#/lib/i18n'

export type ChatMessageView = {
  role: 'user' | 'assistant'
  content: string
}

export function MessageItem({ message }: { message: ChatMessageView }) {
  const { t } = useI18n()
  const [copied, setCopied] = useState(false)

  async function copyMessage() {
    await navigator.clipboard.writeText(message.content)
    setCopied(true)
    window.setTimeout(() => setCopied(false), 2000)
  }

  return (
    <div
      className={`group p-4 ${
        message.role === 'user'
          ? 'bg-neutral-100 dark:bg-neutral-700/70'
          : 'bg-white dark:bg-neutral-800'
      }`}
    >
      <p
        className={`pb-4 text-sm font-medium ${
          message.role === 'user'
            ? 'text-neutral-500 dark:text-neutral-400'
            : 'text-neutral-900 dark:text-neutral-100'
        }`}
      >
        {message.role === 'assistant' ? 'pdfpintar' : t('You')}
      </p>

      <Markdown content={message.content} />

      <div className="flex items-center justify-end gap-3 pt-2">
        <button
          type="button"
          onClick={copyMessage}
          className="invisible flex items-center gap-2 p-1 text-xs opacity-70 hover:opacity-100 group-hover:visible"
        >
          {copied ? (
            <>
              {t('Copied')} <CopyCheck className="h-3 w-3" />
            </>
          ) : (
            <>
              {t('Copy')} <Copy className="h-3 w-3" />
            </>
          )}
        </button>
      </div>
    </div>
  )
}
