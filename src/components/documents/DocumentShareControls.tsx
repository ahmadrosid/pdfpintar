import { useState } from 'react'
import { useRouter } from '@tanstack/react-router'

import { useI18n } from '#/lib/i18n'
import { toggleShare } from '#/server/documents'

type DocumentShareControlsProps = {
  documentId: number
  fileName: string
  isPublic: boolean
  sharingToken: string | null
}

export function DocumentShareControls({
  documentId,
  isPublic,
  sharingToken,
}: DocumentShareControlsProps) {
  const { t } = useI18n()
  const router = useRouter()
  const [copied, setCopied] = useState(false)
  const [pending, setPending] = useState(false)

  async function handleToggleShare() {
    setPending(true)
    try {
      await toggleShare({ data: { documentId } })
      await router.invalidate()
    } finally {
      setPending(false)
    }
  }

  async function handleCopyLink() {
    if (!sharingToken) return

    const shareUrl = `${window.location.origin}/share/${sharingToken}`
    await navigator.clipboard.writeText(shareUrl)
    setCopied(true)
    window.setTimeout(() => setCopied(false), 2000)
  }

  return (
    <div className="flex items-center gap-3 text-sm">
      <button
        type="button"
        className="font-semibold text-[var(--sea-ink)] hover:text-[var(--sea-ink-soft)]"
        disabled={pending}
        onClick={handleToggleShare}
      >
        {isPublic ? t('Make Private') : t('Share')}
      </button>
      {isPublic && sharingToken ? (
        <button
          type="button"
          className="font-semibold text-[var(--sea-ink)] hover:text-[var(--sea-ink-soft)]"
          onClick={handleCopyLink}
        >
          {copied ? t('Copied') : t('Copy Link')}
        </button>
      ) : null}
    </div>
  )
}
