import { describe, expect, it } from 'vitest'

import {
  MAX_PDF_UPLOAD_BYTES,
  validatePdfUpload,
} from '#/lib/documents/validation'

function makeFile(overrides: Partial<File> & { size: number; type: string }) {
  return {
    name: 'sample.pdf',
    ...overrides,
  } as File
}

describe('validatePdfUpload', () => {
  it('accepts a PDF within the size limit', () => {
    expect(
      validatePdfUpload(
        makeFile({ type: 'application/pdf', size: MAX_PDF_UPLOAD_BYTES }),
      ),
    ).toBeNull()
  })

  it('rejects non-PDF files', () => {
    expect(
      validatePdfUpload(
        makeFile({ name: 'photo.png', type: 'image/png', size: 1024 }),
      ),
    ).toBe('invalid_type')
  })

  it('accepts PDFs with an empty MIME type when the extension is .pdf', () => {
    expect(
      validatePdfUpload(
        makeFile({ name: 'resume.pdf', type: '', size: 1024 }),
      ),
    ).toBeNull()
  })

  it('rejects PDFs larger than 10MB', () => {
    expect(
      validatePdfUpload(
        makeFile({
          type: 'application/pdf',
          size: MAX_PDF_UPLOAD_BYTES + 1,
        }),
      ),
    ).toBe('too_large')
  })
})
