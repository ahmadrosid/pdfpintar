import { useEffect, useMemo } from 'react'
import { ProgressBar, Viewer, Worker } from '@react-pdf-viewer/core'
import { defaultLayoutPlugin } from '@react-pdf-viewer/default-layout'

import jumpToPagePlugin from '#/lib/pdf/jump-to-page-plugin'

import '@react-pdf-viewer/core/lib/styles/index.css'
import '@react-pdf-viewer/default-layout/lib/styles/index.css'
import workerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url'

type PdfViewerProps = {
  fileUrl: string
  onExpired?: () => void
}

export function PdfViewer({ fileUrl, onExpired }: PdfViewerProps) {
  const defaultLayoutPluginInstance = defaultLayoutPlugin({
    toolbarPlugin: {
      downloadButton: {
        hidden: true,
      },
    },
  })
  const jumpPluginInstance = useMemo(() => jumpToPagePlugin(), [])

  useEffect(() => {
    function handleJump(event: Event) {
      const detail = (event as CustomEvent<{ pageIndex: number }>).detail
      jumpPluginInstance.jumpToPage(
        detail.pageIndex > 0 ? detail.pageIndex - 1 : 0,
      )
    }

    window.addEventListener('jumpToPage', handleJump)
    return () => window.removeEventListener('jumpToPage', handleJump)
  }, [jumpPluginInstance])

  return (
    <Worker workerUrl={workerUrl}>
      <div className="h-full min-h-[50vh] lg:min-h-[calc(100vh-8rem)]">
        <Viewer
          fileUrl={fileUrl}
          plugins={[defaultLayoutPluginInstance, jumpPluginInstance]}
          renderError={() => (
            <div className="flex h-full flex-col items-center justify-center gap-3 p-6 text-center text-sm text-[var(--sea-ink-soft)]">
              <p>Unable to load the PDF preview.</p>
              {onExpired ? (
                <button type="button" className="demo-button" onClick={onExpired}>
                  Retry
                </button>
              ) : null}
            </div>
          )}
          renderLoader={(percentages) => (
            <div style={{ width: '240px' }}>
              <ProgressBar progress={Math.round(percentages)} />
            </div>
          )}
        />
      </div>
    </Worker>
  )
}
