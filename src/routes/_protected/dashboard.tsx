import { createFileRoute } from '@tanstack/react-router'

import { DocumentListPanel } from '#/components/documents/DocumentListPanel'
import { useI18n } from '#/lib/i18n'

export const Route = createFileRoute('/_protected/dashboard')({
  component: DashboardPage,
})

function DashboardPage() {
  const { t } = useI18n()
  const { session } = Route.useRouteContext()

  return (
    <section className="space-y-6">
      <div>
        <p className="mb-2 text-sm font-medium text-neutral-500 dark:text-neutral-400">
          {t('Dashboard')}
        </p>
        <h1 className="text-3xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50">
          {t('Welcome')}, {session.user.name}
        </h1>
        <p className="mt-2 text-neutral-500 dark:text-neutral-400">
          {t('Your documents')}
        </p>
      </div>
      <DocumentListPanel />
    </section>
  )
}
