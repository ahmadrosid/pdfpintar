import { describe, expect, it, vi } from 'vitest'

import { documents, messages, threads, users } from '#/db/schema'
import { createTestDb } from '#/db/test-utils'
import {
  copyPublicDocument,
  loadPublicDocumentPageData,
  removeDocumentShare,
  toggleDocumentShare,
} from '#/lib/documents/sharing'

vi.mock('#/lib/storage/s3', () => ({
  copyPrivatePdf: vi.fn(async (_source: string, destination: string) => destination),
}))

describe('document sharing', () => {
  it('loads public document data by token without auth', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner@example.com' })
      .returning()

    const [document] = await db
      .insert(documents)
      .values({
        fileId: 'file-public',
        fileName: 'shared.pdf',
        fileSize: 1024,
        filePath: 'documents/shared.pdf',
        userId: owner.id,
        sharingToken: 'share-token-123',
        isPublic: true,
      })
      .returning()

    const [thread] = await db
      .insert(threads)
      .values({
        openaiThreadId: 'thread-public',
        assistantId: 'asst-public',
        documentId: document.id,
      })
      .returning()

    await db.insert(messages).values({
      threadId: thread.id,
      role: 'assistant',
      content: 'Public answer',
    })

    const result = await loadPublicDocumentPageData(db, 'share-token-123')

    expect(result.ok).toBe(true)
    if (!result.ok) return

    expect(result.data.document.fileName).toBe('shared.pdf')
    expect(result.data.ownerUserId).toBe(owner.id)
    expect(result.data.messages).toHaveLength(1)
  })

  it('returns not_found when document is private', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner2@example.com' })
      .returning()

    await db.insert(documents).values({
      fileId: 'file-private',
      fileName: 'private.pdf',
      fileSize: 1024,
      filePath: 'documents/private.pdf',
      userId: owner.id,
      sharingToken: 'private-token',
      isPublic: false,
    })

    const result = await loadPublicDocumentPageData(db, 'private-token')
    expect(result).toEqual({ ok: false, error: 'not_found' })
  })

  it('toggles sharing on first share and generates a token', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner3@example.com' })
      .returning()

    const [document] = await db
      .insert(documents)
      .values({
        fileId: 'file-share',
        fileName: 'toggle.pdf',
        fileSize: 1024,
        userId: owner.id,
      })
      .returning()

    const result = await toggleDocumentShare(db, document.id, owner.id)

    expect(result.ok).toBe(true)
    if (!result.ok) return

    expect(result.document.isPublic).toBe(true)
    expect(result.document.sharingToken).toHaveLength(32)
  })

  it('copies a public document for another authenticated user', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner4@example.com' })
      .returning()
    const [copier] = await db
      .insert(users)
      .values({ name: 'Copier', email: 'copier@example.com' })
      .returning()

    const [document] = await db
      .insert(documents)
      .values({
        fileId: 'file-copy',
        fileName: 'copy-me.pdf',
        fileSize: 2048,
        filePath: 'documents/copy-me.pdf',
        userId: owner.id,
        isPublic: true,
      })
      .returning()

    const result = await copyPublicDocument(db, document.id, copier.id)

    expect(result).toEqual({ ok: true, documentId: 2 })

    const copied = await db.select().from(documents)
    expect(copied).toHaveLength(2)
    expect(copied[1]?.userId).toBe(copier.id)
    expect(copied[1]?.isPublic).toBe(false)
  })

  it('rejects copy on private document', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner5@example.com' })
      .returning()
    const [copier] = await db
      .insert(users)
      .values({ name: 'Copier', email: 'copier2@example.com' })
      .returning()

    const [document] = await db
      .insert(documents)
      .values({
        fileId: 'file-private-copy',
        fileName: 'no-copy.pdf',
        fileSize: 512,
        filePath: 'documents/no-copy.pdf',
        userId: owner.id,
        isPublic: false,
      })
      .returning()

    const result = await copyPublicDocument(db, document.id, copier.id)
    expect(result).toEqual({ ok: false, error: 'forbidden' })
  })

  it('removes sharing token and makes document private', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner6@example.com' })
      .returning()

    await db.insert(documents).values({
      fileId: 'file-remove',
      fileName: 'remove-share.pdf',
      fileSize: 512,
      userId: owner.id,
      sharingToken: 'remove-me',
      isPublic: true,
    })

    const result = await removeDocumentShare(db, 'remove-me', owner.id)
    expect(result).toEqual({ ok: true })

    const publicResult = await loadPublicDocumentPageData(db, 'remove-me')
    expect(publicResult).toEqual({ ok: false, error: 'not_found' })
  })
})
