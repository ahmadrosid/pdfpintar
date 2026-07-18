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
        makeFile({ type: 'image/png', size: 1024 }),
      ),
    ).toBe('invalid_type')
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
