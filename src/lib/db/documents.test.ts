import { describe, expect, it } from 'vitest'
import { eq } from 'drizzle-orm'

import { documents, messages, threads, users } from '#/db/schema'
import { createTestDb } from '#/db/test-utils'
import {
  countMessagesForThread,
  deleteDocumentForUser,
  documentHasThreads,
} from '#/lib/db/documents'

describe('documents db', () => {
  it('inserts user document thread and messages', async () => {
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
        userId: user.id,
      })
      .returning()

    const [thread] = await db
      .insert(threads)
      .values({
        openaiThreadId: 'thread-1',
        assistantId: 'asst-1',
        documentId: document.id,
      })
      .returning()

    await db.insert(messages).values([
      { threadId: thread.id, role: 'user', content: 'Hello' },
      { threadId: thread.id, role: 'assistant', content: 'Hi' },
    ])

    expect(await documentHasThreads(db, document.id)).toBe(true)
    expect(await countMessagesForThread(db, thread.id)).toBe(2)
  })

  it('cascades delete from document to threads and messages', async () => {
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

    const [thread] = await db
      .insert(threads)
      .values({
        openaiThreadId: 'thread-2',
        assistantId: 'asst-2',
        documentId: document.id,
      })
      .returning()

    await db.insert(messages).values({
      threadId: thread.id,
      role: 'user',
      content: 'Question',
    })

    const result = await deleteDocumentForUser(db, document.id, user.id)
    expect(result).toEqual({ ok: true })

    const remainingThreads = await db
      .select()
      .from(threads)
      .where(eq(threads.documentId, document.id))
    const remainingMessages = await db.select().from(messages)

    expect(remainingThreads).toHaveLength(0)
    expect(remainingMessages).toHaveLength(0)
  })

  it('rejects delete for non-owner', async () => {
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

    const result = await deleteDocumentForUser(db, document.id, other.id)
    expect(result).toEqual({ ok: false, error: 'forbidden' })
  })

  it('rejects message insert with invalid thread id', async () => {
    const { db } = await createTestDb()

    await expect(
      db.insert(messages).values({
        threadId: 9999,
        role: 'user',
        content: 'orphan',
      }),
    ).rejects.toThrow()
  })
})
