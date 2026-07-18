import { describe, expect, it } from 'vitest'

import { documents, messages, threads, users } from '#/db/schema'
import { createTestDb } from '#/db/test-utils'
import { loadDocumentPageData } from '#/lib/documents/page-data'

describe('loadDocumentPageData', () => {
  it('returns document, thread, and messages for owner', async () => {
    const { db } = await createTestDb()

    const [user] = await db
      .insert(users)
      .values({ name: 'Ada', email: 'ada@example.com' })
      .returning()

    const [document] = await db
      .insert(documents)
      .values({
        fileId: 'file-1',
        fileName: 'sample.pdf',
        fileSize: 1024,
        filePath: 'documents/sample.pdf',
        userId: user.id,
      })
      .returning()

    const [thread] = await db
      .insert(threads)
      .values({
        openaiThreadId: 'thread-1',
        assistantId: 'asst-1',
        documentId: document.id,
        title: 'Budget review',
      })
      .returning()

    await db.insert(messages).values([
      { threadId: thread.id, role: 'user', content: 'Summarize page 1' },
      { threadId: thread.id, role: 'assistant', content: 'Here is the summary.' },
    ])

    const result = await loadDocumentPageData(db, document.id, user.id)

    expect(result).toEqual({
      ok: true,
      data: {
        document: {
          id: document.id,
          fileName: 'sample.pdf',
          fileId: 'file-1',
          isPublic: false,
          sharingToken: null,
        },
        thread: {
          id: thread.id,
          openaiThreadId: 'thread-1',
          assistantId: 'asst-1',
          documentId: document.id,
          title: 'Budget review',
        },
        messages: [
          { id: 1, role: 'user', content: 'Summarize page 1' },
          { id: 2, role: 'assistant', content: 'Here is the summary.' },
        ],
      },
    })
  })

  it('returns empty thread and messages when no chat exists', async () => {
    const { db } = await createTestDb()

    const [user] = await db
      .insert(users)
      .values({ name: 'Bob', email: 'bob@example.com' })
      .returning()

    const [document] = await db
      .insert(documents)
      .values({
        fileId: 'file-2',
        fileName: 'notes.pdf',
        fileSize: 2048,
        userId: user.id,
      })
      .returning()

    const result = await loadDocumentPageData(db, document.id, user.id)

    expect(result).toEqual({
      ok: true,
      data: {
        document: {
          id: document.id,
          fileName: 'notes.pdf',
          fileId: 'file-2',
          isPublic: false,
          sharingToken: null,
        },
        thread: null,
        messages: [],
      },
    })
  })

  it('returns not_found for missing document', async () => {
    const { db } = await createTestDb()

    const [user] = await db
      .insert(users)
      .values({ name: 'Cara', email: 'cara@example.com' })
      .returning()

    const result = await loadDocumentPageData(db, 999, user.id)
    expect(result).toEqual({ ok: false, error: 'not_found' })
  })

  it('returns forbidden for non-owner', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner@example.com' })
      .returning()
    const [other] = await db
      .insert(users)
      .values({ name: 'Other', email: 'other@example.com' })
      .returning()

    const [document] = await db
      .insert(documents)
      .values({
        fileId: 'file-3',
        fileName: 'private.pdf',
        fileSize: 512,
        userId: owner.id,
      })
      .returning()

    const result = await loadDocumentPageData(db, document.id, other.id)
    expect(result).toEqual({ ok: false, error: 'forbidden' })
  })
})
