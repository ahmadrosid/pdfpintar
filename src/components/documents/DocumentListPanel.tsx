import { FileIcon, FileUp, Trash2 } from 'lucide-react'
import { useEffect, useState } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { Link } from '@tanstack/react-router'

import { validatePdfUpload } from '#/lib/documents/validation'
import { useI18n } from '#/lib/i18n'
import {
  deleteDocument,
  listDocuments,
  uploadDocument,
} from '#/server/documents'

export function DocumentListPanel() {
  const { t } = useI18n()
  const queryClient = useQueryClient()
  const [search, setSearch] = useState('')
  const [debouncedSearch, setDebouncedSearch] = useState('')
  const [deleteId, setDeleteId] = useState<number | null>(null)

  useEffect(() => {
    const timer = window.setTimeout(() => setDebouncedSearch(search), 300)
    return () => window.clearTimeout(timer)
  }, [search])

  const documentsQuery = useQuery({
    queryKey: ['documents', debouncedSearch],
    queryFn: () => listDocuments({ data: { search: debouncedSearch } }),
  })

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteDocument({ data: { id } }),
    onSuccess: async () => {
      setDeleteId(null)
      await queryClient.invalidateQueries({ queryKey: ['documents'] })
    },
  })

  const documents = documentsQuery.data ?? []

  return (
    <div className="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-950">
      <div className="flex flex-col gap-3 border-b border-[var(--line)] p-4 sm:flex-row sm:items-center sm:justify-between">
        <input
          type="search"
          value={search}
          onChange={(event) => setSearch(event.target.value)}
          placeholder={t('Search documents')}
          className="demo-input w-full max-w-sm"
        />
        <DocumentUploadButton
          onUploaded={() =>
            queryClient.invalidateQueries({ queryKey: ['documents'] })
          }
        />
      </div>

      <div>
        {documentsQuery.isLoading ? (
          <p className="p-6 text-center text-sm text-[var(--sea-ink-soft)]">
            …
          </p>
        ) : null}
        {!documentsQuery.isLoading && documents.length === 0 ? (
          <p className="p-6 text-center text-sm text-[var(--sea-ink-soft)]">
            {t('No documents found.')}
          </p>
        ) : null}
        {documents.map((document) => (
          <div
            key={document.id}
            className="group flex items-center gap-3 border-b border-[var(--line)] px-4 py-3 last:border-b-0 hover:bg-black/5"
          >
            <FileIcon className="h-5 w-5 shrink-0 opacity-50" />
            <div className="min-w-0 flex-1">
              <Link
                to="/documents/$documentId"
                params={{ documentId: String(document.id) }}
                className="block truncate font-medium text-[var(--sea-ink)] group-hover:underline"
              >
                {document.fileName}
              </Link>
              <p className="text-sm text-[var(--sea-ink-soft)]">
                {document.createdAt}
              </p>
            </div>
            <button
              type="button"
              onClick={() => setDeleteId(document.id)}
              className="rounded-lg p-2 text-[var(--sea-ink-soft)] transition hover:text-red-600"
              aria-label={t('Delete Document')}
            >
              <Trash2 className="h-5 w-5" />
            </button>
          </div>
        ))}
        <p className="px-4 py-3 text-sm text-[var(--sea-ink-soft)]">
          {t('Click to chat with the document')}
        </p>
      </div>

      {deleteId !== null ? (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
          <div className="w-full max-w-md rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-950">
            <h2 className="mb-2 text-lg font-semibold text-[var(--sea-ink)]">
              {t('Delete Document')}
            </h2>
            <p className="mb-1 text-sm text-[var(--sea-ink-soft)]">
              {t('Are you sure you want to delete this document?')}
            </p>
            <p className="mb-6 text-sm text-[var(--sea-ink-soft)]">
              {t('This action cannot be undone.')}
            </p>
            <div className="flex justify-end gap-3">
              <button
                type="button"
                className="rounded-lg px-4 py-2 text-sm font-semibold text-neutral-500 hover:bg-neutral-50 dark:text-neutral-400 dark:hover:bg-neutral-900"
                onClick={() => setDeleteId(null)}
              >
                {t('Cancel')}
              </button>
              <button
                type="button"
                className="demo-button"
                disabled={deleteMutation.isPending}
                onClick={() => deleteMutation.mutate(deleteId)}
              >
                {t('Delete')}
              </button>
            </div>
          </div>
        </div>
      ) : null}
    </div>
  )
}

function DocumentUploadButton({
  onUploaded,
}: {
  onUploaded: () => void
}) {
  const { t } = useI18n()
  const [open, setOpen] = useState(false)
  const [file, setFile] = useState<File | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [uploading, setUploading] = useState(false)

  function reset() {
    setOpen(false)
    setFile(null)
    setError(null)
    setUploading(false)
  }

  async function handleUpload() {
    if (!file) return

    const validationError = validatePdfUpload(file)
    if (validationError) {
      setError(t('Only PDF files up to 10MB are allowed.'))
      return
    }

    setUploading(true)
    setError(null)

    try {
      const formData = new FormData()
      formData.append('document', file)
      const response = await uploadDocument({ data: formData })

      if (response.message === 'Documents uploaded successfully') {
        onUploaded()
        reset()
        return
      }

      setError(t('Upload failed'))
    } catch (error) {
      const message =
        error instanceof Error && error.message.trim()
          ? error.message
          : t('Upload failed')
      setError(message)
    } finally {
      setUploading(false)
    }
  }

  return (
    <>
      <button
        type="button"
        className="demo-button whitespace-nowrap"
        onClick={() => setOpen(true)}
      >
        {t('Upload PDF')}
        <FileUp className="ml-2 inline h-4 w-4" />
      </button>

      {open ? (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
          <div className="w-full max-w-lg rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-950">
            <h2 className="mb-1 text-lg font-semibold text-neutral-900 dark:text-neutral-50">
              {t('Upload Document')}
            </h2>
            <p className="mb-4 text-sm text-neutral-500 dark:text-neutral-400">
              {t('Upload your PDF document here')}
            </p>
            <label className="mb-4 flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-neutral-300 px-6 py-10 text-center dark:border-neutral-700">
              <span className="font-medium text-neutral-900 dark:text-neutral-100">
                {t('Click to upload')}
              </span>
              <span className="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                {t('or drag and drop')}
              </span>
              <input
                type="file"
                accept="application/pdf"
                className="sr-only"
                onChange={(event) => {
                  const selected = event.target.files?.[0] ?? null
                  setFile(selected)
                  setError(null)
                }}
              />
            </label>
            {file ? (
              <p className="mb-4 text-sm text-[var(--sea-ink-soft)]">
                {file.name}
              </p>
            ) : null}
            {error ? (
              <p className="mb-4 text-sm text-red-600" role="alert">
                {error}
              </p>
            ) : null}
            <div className="flex justify-end gap-3">
              <button
                type="button"
                className="rounded-lg px-4 py-2 text-sm font-semibold text-neutral-500 hover:bg-neutral-50 dark:text-neutral-400 dark:hover:bg-neutral-900"
                onClick={reset}
              >
                {t('Cancel')}
              </button>
              <button
                type="button"
                className="demo-button"
                disabled={!file || uploading}
                onClick={handleUpload}
              >
                {uploading ? t('Uploading') : t('Upload')}
              </button>
            </div>
          </div>
        </div>
      ) : null}
    </>
  )
}
