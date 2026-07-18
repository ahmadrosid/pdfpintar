import { createServerFn } from '@tanstack/react-start'

import {
  buildPdfFileName,
  markdownToPdfBuffer,
  validateMarkdownInput,
} from '#/lib/pdf/markdown-to-pdf'

export const generateMarkdownPdf = createServerFn({ method: 'POST' })
  .inputValidator((data: { markdown: string }) => data)
  .handler(async ({ data }) => {
    const validationError = validateMarkdownInput(data.markdown)
    if (validationError) {
      throw new Error(validationError)
    }

    const buffer = await markdownToPdfBuffer(data.markdown)

    return {
      fileName: buildPdfFileName(),
      base64: Buffer.from(buffer).toString('base64'),
    }
  })
