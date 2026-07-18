import { describe, expect, it } from 'vitest'
import { eq } from 'drizzle-orm'

import { documents, users } from '#/db/schema'
import { createTestDb } from '#/db/test-utils'
import {
  deleteDocumentForUser,
  getOwnedDocument,
} from '#/lib/db/documents'
import { loadPublicDocumentPageData } from '#/lib/documents/sharing'

describe('document access policies', () => {
  it('allows owners to read their documents', async () => {
    const { db } = await createTestDb()

    const [user] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner@example.com' })
      .returning()

    const [document] = await db
      .insert(documents)
      .values({
        fileId: 'file-owner',
        fileName: 'owner.pdf',
        fileSize: 1024,
        userId: user.id,
      })
      .returning()

    const owned = await getOwnedDocument(db, document.id, user.id)
    expect(owned?.id).toBe(document.id)
  })

  it('denies non-owners from reading private documents', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner2@example.com' })
      .returning()
    const [other] = await db
      .insert(users)
      .values({ name: 'Other', email: 'other@example.com' })
      .returning()

    const [document] = await db
      .insert(documents)
      .values({
        fileId: 'file-private',
        fileName: 'private.pdf',
        fileSize: 512,
        userId: owner.id,
      })
      .returning()

    const owned = await getOwnedDocument(db, document.id, other.id)
    expect(owned).toBeNull()
  })

  it('allows public reads only when sharing is enabled', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner3@example.com' })
      .returning()

    await db.insert(documents).values({
      fileId: 'file-public',
      fileName: 'public.pdf',
      fileSize: 512,
      filePath: 'documents/public.pdf',
      userId: owner.id,
      sharingToken: 'public-token',
      isPublic: true,
    })

    const publicResult = await loadPublicDocumentPageData(db, 'public-token')
    expect(publicResult.ok).toBe(true)

    await db
      .update(documents)
      .set({ isPublic: false })
      .where(eq(documents.sharingToken, 'public-token'))

    const privateResult = await loadPublicDocumentPageData(db, 'public-token')
    expect(privateResult).toEqual({ ok: false, error: 'not_found' })
  })

  it('only lets owners delete documents', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner4@example.com' })
      .returning()
    const [other] = await db
      .insert(users)
      .values({ name: 'Other', email: 'other2@example.com' })
      .returning()

    const [document] = await db
      .insert(documents)
      .values({
        fileId: 'file-delete',
        fileName: 'delete.pdf',
        fileSize: 256,
        userId: owner.id,
      })
      .returning()

    expect(await deleteDocumentForUser(db, document.id, other.id)).toEqual({
      ok: false,
      error: 'forbidden',
    })
    expect(await deleteDocumentForUser(db, document.id, owner.id)).toEqual({
      ok: true,
    })
  })
})
