import { lazy, Suspense } from 'react'
import { Link, createFileRoute, notFound, redirect } from '@tanstack/react-router'

import { ChatPage } from '#/components/chat/chat-page'
import { DocumentShareControls } from '#/components/documents/DocumentShareControls'
import { useI18n } from '#/lib/i18n'
import { getDocumentPageData } from '#/server/documents'

const PdfViewer = lazy(() =>
  import('#/components/pdf/pdf-viewer').then((module) => ({
    default: module.PdfViewer,
  })),
)

export const Route = createFileRoute('/_protected/documents/$documentId')({
  loader: async ({ params }) => {
    const documentId = Number(params.documentId)
    if (!Number.isFinite(documentId)) {
      throw notFound()
    }

    try {
      return await getDocumentPageData({ data: { documentId } })
    } catch (error) {
      const message = error instanceof Error ? error.message : 'unknown'
      if (message === 'not_found') {
        throw notFound()
      }
      if (message === 'forbidden') {
        throw redirect({ to: '/dashboard' })
      }
      throw error
    }
  },
  component: DocumentPage,
})

function DocumentPage() {
  const { t } = useI18n()
  const pageData = Route.useLoaderData()
  const router = Route.useRouter()

  return (
    <div className="flex h-full flex-col">
      <div className="flex items-center justify-between px-2 py-3">
        <Link
          to="/dashboard"
          className="text-sm font-semibold text-[var(--sea-ink)] hover:text-[var(--sea-ink-soft)]"
        >
          ← {pageData.document.fileName}
        </Link>
        <DocumentShareControls
          documentId={pageData.document.id}
          fileName={pageData.document.fileName}
          isPublic={pageData.document.isPublic}
          sharingToken={pageData.document.sharingToken}
        />
      </div>

      <div className="grid h-auto flex-1 gap-2 p-2 lg:grid-cols-2 lg:h-[calc(100vh-8rem)]">
        <div className="order-2 flex min-h-[50vh] flex-col lg:order-1 lg:min-h-0">
          <ChatPage
            documentId={pageData.document.id}
            initialMessages={pageData.messages}
            initialThread={pageData.thread}
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
