import { describe, expect, it } from 'vitest'

import {
  buildPdfFileName,
  markdownToPdfBuffer,
  parseMarkdownBlocks,
  validateMarkdownInput,
} from '#/lib/pdf/markdown-to-pdf'

describe('markdown-to-pdf', () => {
  it('rejects empty markdown', () => {
    expect(validateMarkdownInput('   ')).toBe('empty')
  })

  it('parses headings and list items', () => {
    expect(parseMarkdownBlocks('# Title\n\nBody\n- item')).toEqual([
      { type: 'h1', text: 'Title' },
      { type: 'p', text: ' ' },
      { type: 'p', text: 'Body' },
      { type: 'li', text: '• item' },
    ])
  })

  it('generates a PDF buffer from markdown', async () => {
    const buffer = await markdownToPdfBuffer('# Hello\n\nPDFPintar')
    expect(buffer.byteLength).toBeGreaterThan(500)
    expect(buffer.subarray(0, 4).toString()).toBe('%PDF')
  })

  it('builds a timestamped file name', () => {
    expect(buildPdfFileName(new Date('2026-07-18T14:30:00.000Z'))).toBe(
      '2026-07-18-14-30-00.pdf',
    )
  })
})
