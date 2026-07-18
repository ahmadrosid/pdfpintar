import { randomBytes } from 'node:crypto'

import { and, asc, desc, eq, isNotNull } from 'drizzle-orm'
import type { NodePgDatabase } from 'drizzle-orm/node-postgres'

import * as schema from '#/db/schema'
import { documents, messages, threads } from '#/db/schema'
import { getOwnedDocument } from '#/lib/db/documents'
import type { ChatMessage, ChatThread, DocumentPageData } from '#/lib/documents/page-data'
import { copyPrivatePdf } from '#/lib/storage/s3'

type Db = NodePgDatabase<typeof schema>

export type SharedDocumentListItem = {
  id: number
  fileName: string
  isPublic: boolean
  sharingToken: string | null
  updatedAt: string
}

export type PublicDocumentPageData = DocumentPageData & {
  ownerUserId: number
}

function mapThread(thread: typeof threads.$inferSelect): ChatThread {
  return {
    id: thread.id,
    openaiThreadId: thread.openaiThreadId,
    assistantId: thread.assistantId,
    documentId: thread.documentId,
    title: thread.title,
  }
}

async function loadThreadMessages(db: Db, threadId: number) {
  const rows = await db
    .select()
    .from(messages)
    .where(eq(messages.threadId, threadId))
    .orderBy(asc(messages.id))

  return rows.map(
    (message): ChatMessage => ({
      id: message.id,
      role: message.role,
      content: message.content,
    }),
  )
}

export function createSharingToken() {
  return randomBytes(16).toString('hex')
}

export function buildCopiedDocumentObjectKey(userId: number) {
  return `documents/${userId}/${randomBytes(20).toString('hex')}.pdf`
}

export async function loadPublicDocumentPageData(
  db: Db,
  token: string,
): Promise<
  | { ok: true; data: PublicDocumentPageData }
  | { ok: false; error: 'not_found' }
> {
  const [document] = await db
    .select()
    .from(documents)
    .where(
      and(eq(documents.sharingToken, token), eq(documents.isPublic, true)),
    )
    .limit(1)

  if (!document?.filePath) {
    return { ok: false, error: 'not_found' }
  }

  const [thread] = await db
    .select()
    .from(threads)
    .where(eq(threads.documentId, document.id))
    .orderBy(desc(threads.createdAt))
    .limit(1)

  const messageRows = thread ? await loadThreadMessages(db, thread.id) : []

  return {
    ok: true,
    data: {
      document: {
        id: document.id,
        fileName: document.fileName,
        fileId: document.fileId,
        isPublic: document.isPublic,
        sharingToken: document.sharingToken,
      },
      thread: thread ? mapThread(thread) : null,
      messages: messageRows,
      ownerUserId: document.userId,
    },
  }
}

export async function listSharedDocumentsForUser(db: Db, userId: number) {
  const rows = await db
    .select()
    .from(documents)
    .where(
      and(eq(documents.userId, userId), isNotNull(documents.sharingToken)),
    )
    .orderBy(desc(documents.updatedAt))

  return rows.map(
    (row): SharedDocumentListItem => ({
      id: row.id,
      fileName: row.fileName,
      isPublic: row.isPublic,
      sharingToken: row.sharingToken,
      updatedAt: row.updatedAt.toISOString(),
    }),
  )
}

export async function toggleDocumentShare(
  db: Db,
  documentId: number,
  userId: number,
) {
  const document = await getOwnedDocument(db, documentId, userId)
  if (!document) {
    return { ok: false as const, error: 'forbidden' as const }
  }

  if (!document.sharingToken) {
    const sharingToken = createSharingToken()
    const [updated] = await db
      .update(documents)
      .set({
        sharingToken,
        isPublic: true,
        updatedAt: new Date(),
      })
      .where(eq(documents.id, document.id))
      .returning()

    return {
      ok: true as const,
      document: updated,
      message: 'Document is now public',
    }
  }

  const [updated] = await db
    .update(documents)
    .set({
      isPublic: !document.isPublic,
      updatedAt: new Date(),
    })
    .where(eq(documents.id, document.id))
    .returning()

  return {
    ok: true as const,
    document: updated,
    message: updated.isPublic
      ? 'Document is now public'
      : 'Document is now private',
  }
}

export async function removeDocumentShare(
  db: Db,
  sharingToken: string,
  userId: number,
) {
  const [document] = await db
    .select()
    .from(documents)
    .where(eq(documents.sharingToken, sharingToken))
    .limit(1)

  if (!document || document.userId !== userId) {
    return { ok: false as const, error: 'forbidden' as const }
  }

  await db
    .update(documents)
    .set({
      sharingToken: null,
      isPublic: false,
      updatedAt: new Date(),
    })
    .where(eq(documents.id, document.id))

  return { ok: true as const }
}

export async function copyPublicDocument(
  db: Db,
  documentId: number,
  userId: number,
) {
  const [document] = await db
    .select()
    .from(documents)
    .where(eq(documents.id, documentId))
    .limit(1)

  if (!document) {
    return { ok: false as const, error: 'not_found' as const }
  }

  if (!document.isPublic || !document.filePath) {
    return { ok: false as const, error: 'forbidden' as const }
  }

  const destinationKey = buildCopiedDocumentObjectKey(userId)
  await copyPrivatePdf(document.filePath, destinationKey)

  const [created] = await db
    .insert(documents)
    .values({
      fileId: document.fileId,
      fileName: document.fileName,
      fileSize: document.fileSize,
      filePath: destinationKey,
      userId,
      isPublic: false,
    })
    .returning()

  return { ok: true as const, documentId: created.id }
}
