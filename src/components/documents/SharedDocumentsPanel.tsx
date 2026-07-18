import { Check, Copy, Lock, Trash2, Unlock } from 'lucide-react'
import { useState } from 'react'
import { Link } from '@tanstack/react-router'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'

import { useI18n } from '#/lib/i18n'
import {
  listSharedDocuments,
  removeShare,
  toggleShare,
} from '#/server/documents'

export function SharedDocumentsPanel() {
  const { t } = useI18n()
  const queryClient = useQueryClient()
  const [copiedToken, setCopiedToken] = useState<string | null>(null)

  const sharedQuery = useQuery({
    queryKey: ['shared-documents'],
    queryFn: () => listSharedDocuments(),
  })

  const toggleMutation = useMutation({
    mutationFn: (documentId: number) => toggleShare({ data: { documentId } }),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['shared-documents'] })
    },
  })

  const removeMutation = useMutation({
    mutationFn: (sharingToken: string) => removeShare({ data: { sharingToken } }),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['shared-documents'] })
    },
  })

  const documents = sharedQuery.data ?? []

  async function copyShareLink(token: string) {
    const shareUrl = `${window.location.origin}/share/${token}`
    await navigator.clipboard.writeText(shareUrl)
    setCopiedToken(token)
    window.setTimeout(() => setCopiedToken(null), 2000)
  }

  return (
    <section className="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-950">
      <div className="border-b border-neutral-200 px-4 py-4 dark:border-neutral-800">
        <h1 className="text-xl font-semibold text-neutral-900 dark:text-neutral-50">
          {t('Shared Documents')}
        </h1>
      </div>

      {sharedQuery.isLoading ? (
        <p className="p-6 text-center text-sm text-[var(--sea-ink-soft)]">…</p>
      ) : null}

      {!sharedQuery.isLoading && documents.length === 0 ? (
        <p className="p-6 text-center text-sm text-[var(--sea-ink-soft)]">
          {t('No shared documents yet.')}
        </p>
      ) : null}

      {documents.map((document) => (
        <div
          key={document.id}
          className="flex flex-col gap-3 border-b border-[var(--line)] px-4 py-3 last:border-b-0 sm:flex-row sm:items-center"
        >
          <div className="min-w-0 flex-1">
            <Link
              to="/documents/$documentId"
              params={{ documentId: String(document.id) }}
              className="font-medium text-[var(--sea-ink)] hover:underline"
            >
              {document.fileName}
            </Link>
            <div className="mt-1 flex flex-wrap items-center gap-2 text-sm text-[var(--sea-ink-soft)]">
              <span
                className={`rounded-full px-2 py-0.5 text-xs font-semibold ${
                  document.isPublic
                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                    : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                }`}
              >
                {document.isPublic ? t('Public') : t('Private')}
              </span>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <button
              type="button"
              className="rounded-lg p-2 text-[var(--sea-ink-soft)] hover:text-[var(--sea-ink)]"
              title={document.isPublic ? t('Make Private') : t('Make Public')}
              onClick={() => toggleMutation.mutate(document.id)}
            >
              {document.isPublic ? (
                <Unlock className="h-5 w-5" />
              ) : (
                <Lock className="h-5 w-5" />
              )}
            </button>
            {document.isPublic && document.sharingToken ? (
              <button
                type="button"
                className="rounded-lg p-2 text-[var(--sea-ink-soft)] hover:text-[var(--sea-ink)]"
                title={t('Copy Share Link')}
                onClick={() => copyShareLink(document.sharingToken!)}
              >
                {copiedToken === document.sharingToken ? (
                  <Check className="h-5 w-5" />
                ) : (
                  <Copy className="h-5 w-5" />
                )}
              </button>
            ) : null}
            {document.sharingToken ? (
              <button
                type="button"
                className="rounded-lg p-2 text-[var(--sea-ink-soft)] hover:text-red-600"
                title={t('Delete Shared Document')}
                onClick={() => removeMutation.mutate(document.sharingToken!)}
              >
                <Trash2 className="h-5 w-5" />
              </button>
            ) : null}
          </div>
        </div>
      ))}
    </section>
  )
}
