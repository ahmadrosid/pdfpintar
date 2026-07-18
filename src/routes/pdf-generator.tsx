import { createFileRoute } from '@tanstack/react-router'
import { useMemo, useState } from 'react'

import { Markdown } from '#/components/chat/markdown'
import { useI18n } from '#/lib/i18n'
import { generateMarkdownPdf } from '#/server/pdf-generator'

export const Route = createFileRoute('/pdf-generator')({
  component: PdfGeneratorPage,
})

function PdfGeneratorPage() {
  const { t } = useI18n()
  const [markdown, setMarkdown] = useState('')
  const [error, setError] = useState<string | null>(null)
  const [pending, setPending] = useState(false)

  const preview = useMemo(() => markdown.trim(), [markdown])

  async function handleDownload() {
    setError(null)
    setPending(true)

    try {
      const result = await generateMarkdownPdf({ data: { markdown } })
      const bytes = Uint8Array.from(atob(result.base64), (char) =>
        char.charCodeAt(0),
      )
      const blob = new Blob([bytes], { type: 'application/pdf' })
      const url = URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = result.fileName
      link.click()
      URL.revokeObjectURL(url)
    } catch (caught) {
      const message = caught instanceof Error ? caught.message : 'failed'
      setError(
        message === 'empty'
          ? t('Markdown content is required.')
          : t('PDF generation failed.'),
      )
    } finally {
      setPending(false)
    }
  }

  return (
    <main className="page-wrap px-4 py-10">
      <section className="mx-auto max-w-4xl space-y-6">
        <div className="text-center">
          <h1 className="text-3xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50">
            {t('Markdown to PDF Converter')}
          </h1>
        </div>

        <div className="grid gap-6 lg:grid-cols-2">
          <label className="block text-sm">
            <span className="mb-2 block font-medium text-neutral-900 dark:text-neutral-100">
              {t('Markdown Content')}
            </span>
            <textarea
              rows={14}
              value={markdown}
              onChange={(event) => setMarkdown(event.target.value)}
              placeholder={t('Enter your Markdown text here...')}
              className="demo-input min-h-[320px] w-full font-mono text-sm"
            />
          </label>

          <div>
            <p className="mb-2 text-sm font-medium text-neutral-900 dark:text-neutral-100">
              {t('Preview')}
            </p>
            <div className="min-h-[320px] rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-950">
              {preview ? (
                <Markdown content={preview} />
              ) : (
                <p className="text-sm text-neutral-500 dark:text-neutral-400">
                  {t('Enter your Markdown text here...')}
                </p>
              )}
            </div>
          </div>
        </div>

        <div className="flex justify-end">
          <button
            type="button"
            className="demo-button"
            disabled={pending}
            onClick={handleDownload}
          >
            {pending ? t('Converting...') : t('Convert to PDF')}
          </button>
        </div>

        {error ? (
          <p className="text-right text-sm text-red-600" role="alert">
            {error}
          </p>
        ) : null}
      </section>
    </main>
  )
}
