import { createFileRoute } from '@tanstack/react-router'

import { SharedDocumentsPanel } from '#/components/documents/SharedDocumentsPanel'
import { useI18n } from '#/lib/i18n'

export const Route = createFileRoute('/_protected/shared-documents')({
  component: SharedDocumentsPage,
})

function SharedDocumentsPage() {
  const { t } = useI18n()

  return (
    <section className="space-y-4">
      <div>
        <p className="mb-2 text-sm font-medium text-neutral-500 dark:text-neutral-400">
          {t('Shared')}
        </p>
        <h1 className="text-3xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50">
          {t('Shared chats')}
        </h1>
      </div>
      <SharedDocumentsPanel />
    </section>
  )
}
