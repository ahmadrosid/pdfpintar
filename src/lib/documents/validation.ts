export const MAX_PDF_UPLOAD_BYTES = 10 * 1024 * 1024

export function isPdfUpload(file: Pick<File, 'name' | 'type'>) {
  return (
    file.type === 'application/pdf' ||
    file.type === 'application/x-pdf' ||
    file.name.toLowerCase().endsWith('.pdf')
  )
}

export function validatePdfUpload(file: Pick<File, 'name' | 'type' | 'size'>) {
  if (!isPdfUpload(file)) {
    return 'invalid_type' as const
  }

  if (file.size > MAX_PDF_UPLOAD_BYTES) {
    return 'too_large' as const
  }

  return null
}
