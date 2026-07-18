import { ArrowUp, Loader2 } from 'lucide-react'
import { useEffect, useRef } from 'react'

import { useI18n } from '#/lib/i18n'

type ChatInputProps = {
  text: string
  isLoading: boolean
  onTextChange: (value: string) => void
  onSend: () => void
}

export function ChatInput({
  text,
  isLoading,
  onTextChange,
  onSend,
}: ChatInputProps) {
  const { t } = useI18n()
  const textareaRef = useRef<HTMLTextAreaElement>(null)

  useEffect(() => {
    const node = textareaRef.current
    if (!node) return

    node.style.height = '0px'
    node.style.height = `${node.scrollHeight}px`
  }, [text])

  function handleKeyDown(event: React.KeyboardEvent<HTMLTextAreaElement>) {
    if (event.key === 'Enter' && !(event.metaKey || event.shiftKey)) {
      event.preventDefault()
      if (!isLoading && text.trim()) {
        onSend()
      }
    }
  }

  return (
    <div className="sticky bottom-0 left-0 right-0 p-4">
      <div className="rounded-xl border border-neutral-300 bg-white p-3 dark:border-neutral-600 dark:bg-neutral-700">
        <textarea
          ref={textareaRef}
          rows={1}
          value={text}
          onChange={(event) => onTextChange(event.target.value)}
          onKeyDown={handleKeyDown}
          placeholder={isLoading ? t('Thinking...') : t('Type your message here...')}
          className="mb-2 max-h-96 w-full resize-none border-0 bg-transparent px-1 py-1 focus:outline-none focus-visible:ring-0"
        />
        <div className="flex justify-end">
          <button
            type="button"
            onClick={onSend}
            disabled={isLoading || text.trim().length === 0}
            className={`rounded-lg bg-neutral-900 p-1.5 text-white ${
              text.trim().length === 0 && !isLoading ? 'opacity-30' : 'opacity-100'
            } hover:bg-black disabled:cursor-not-allowed`}
          >
            {isLoading ? (
              <Loader2 className="h-4 w-4 animate-spin" />
            ) : (
              <ArrowUp className="h-4 w-4" />
            )}
          </button>
        </div>
      </div>
    </div>
  )
}
