import { lazy, Suspense } from 'react'
import { createFileRoute, notFound } from '@tanstack/react-router'

import { PublicChatInterface } from '#/components/chat/public-chat-interface'
import { useI18n } from '#/lib/i18n'
import { getPublicDocumentPageData } from '#/server/documents'

const PdfViewer = lazy(() =>
  import('#/components/pdf/pdf-viewer').then((module) => ({
    default: module.PdfViewer,
  })),
)

export const Route = createFileRoute('/share/$token')({
  loader: async ({ params }) => {
    try {
      return await getPublicDocumentPageData({ data: { token: params.token } })
    } catch (error) {
      const message = error instanceof Error ? error.message : 'unknown'
      if (message === 'not_found') {
        throw notFound()
      }
      throw notFound()
    }
  },
  component: PublicSharePage,
})

function PublicSharePage() {
  const { t } = useI18n()
  const pageData = Route.useLoaderData()
  const router = Route.useRouter()

  return (
    <div className="-mx-4 flex min-h-[calc(100vh-5rem)] flex-col">
      <div className="flex items-center justify-between px-2 py-3">
        <div>
          <p className="text-sm font-semibold text-[var(--sea-ink)]">
            {pageData.document.fileName}
          </p>
          <p className="text-xs text-[var(--sea-ink-soft)]">
            {t('Shared Document')}
          </p>
        </div>
      </div>

      <div className="grid h-auto flex-1 gap-2 p-2 lg:grid-cols-2 lg:h-[calc(100vh-8rem)]">
        <div className="order-2 flex min-h-[50vh] flex-col lg:order-1 lg:min-h-0">
          <PublicChatInterface
            documentId={pageData.document.id}
            documentName={pageData.document.fileName}
            ownerUserId={pageData.ownerUserId}
            messages={pageData.messages}
          />
        </div>
        <div className="order-1 min-h-[50vh] overflow-hidden rounded-2xl border border-[var(--line)] bg-white lg:order-2 lg:min-h-0 dark:bg-neutral-900">
          <Suspense
            fallback={
              <div className="flex h-full items-center justify-center text-sm text-[var(--sea-ink-soft)]">
                {t('Loading PDF...')}
              </div>
            }
          >
            <PdfViewer
              fileUrl={pageData.pdfUrl}
              onExpired={() => router.invalidate()}
            />
          </Suspense>
        </div>
      </div>
    </div>
  )
}
