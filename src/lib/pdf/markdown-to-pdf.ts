import React from 'react'
import {
  Document,
  Page,
  StyleSheet,
  Text,
  renderToBuffer,
} from '@react-pdf/renderer'
import { marked } from 'marked'

export type MarkdownBlock = {
  type: 'h1' | 'h2' | 'h3' | 'p' | 'li'
  text: string
}

const styles = StyleSheet.create({
  page: {
    padding: 40,
    fontSize: 12,
    lineHeight: 1.5,
  },
  h1: {
    fontSize: 22,
    marginBottom: 10,
    fontWeight: 'bold',
  },
  h2: {
    fontSize: 18,
    marginBottom: 8,
    fontWeight: 'bold',
  },
  h3: {
    fontSize: 14,
    marginBottom: 6,
    fontWeight: 'bold',
  },
  p: {
    marginBottom: 8,
  },
  li: {
    marginBottom: 4,
    paddingLeft: 8,
  },
})

export function validateMarkdownInput(markdown: string) {
  if (!markdown.trim()) {
    return 'empty' as const
  }

  return null
}

export function parseMarkdownBlocks(markdown: string): MarkdownBlock[] {
  return markdown.split('\n').map((line) => {
    if (line.startsWith('### ')) {
      return { type: 'h3', text: line.slice(4) }
    }
    if (line.startsWith('## ')) {
      return { type: 'h2', text: line.slice(3) }
    }
    if (line.startsWith('# ')) {
      return { type: 'h1', text: line.slice(2) }
    }
    if (line.startsWith('- ')) {
      return { type: 'li', text: `• ${line.slice(2)}` }
    }

    return { type: 'p', text: line.trim() === '' ? ' ' : line }
  })
}

export function markdownToHtml(markdown: string) {
  const tableStyles = `
    <style>
      table { width: 100%; border-collapse: collapse; }
      th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
      th { background-color: #f2f2f2; font-weight: bold; }
      tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
  `

  return tableStyles + marked.parse(markdown, { async: false })
}

function blockStyle(type: MarkdownBlock['type']) {
  switch (type) {
    case 'h1':
      return styles.h1
    case 'h2':
      return styles.h2
    case 'h3':
      return styles.h3
    case 'li':
      return styles.li
    default:
      return styles.p
  }
}

export async function markdownToPdfBuffer(markdown: string) {
  const blocks = parseMarkdownBlocks(markdown)

  const document = React.createElement(
    Document,
    null,
    React.createElement(
      Page,
      { size: 'A4', style: styles.page },
      ...blocks.map((block, index) =>
        React.createElement(
          Text,
          { key: String(index), style: blockStyle(block.type) },
          block.text,
        ),
      ),
    ),
  )

  return renderToBuffer(document)
}

export function buildPdfFileName(date = new Date()) {
  const stamp = date
    .toISOString()
    .replace(/[:.]/g, '-')
    .replace('T', '-')
    .slice(0, 19)

  return `${stamp}.pdf`
}
