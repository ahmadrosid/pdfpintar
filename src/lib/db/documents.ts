import { and, eq } from 'drizzle-orm'
import type { NodePgDatabase } from 'drizzle-orm/node-postgres'

import * as schema from '#/db/schema'
import { documents, messages, threads } from '#/db/schema'

type Db = NodePgDatabase<typeof schema>

export async function deleteDocumentForUser(
  db: Db,
  documentId: number,
  userId: number,
) {
  const [doc] = await db
    .select({ id: documents.id, userId: documents.userId })
    .from(documents)
    .where(eq(documents.id, documentId))
    .limit(1)

  if (!doc) {
    return { ok: false as const, error: 'not_found' as const }
  }

  if (doc.userId !== userId) {
    return { ok: false as const, error: 'forbidden' as const }
  }

  await db.delete(documents).where(eq(documents.id, documentId))

  return { ok: true as const }
}

export async function documentHasThreads(db: Db, documentId: number) {
  const rows = await db
    .select({ id: threads.id })
    .from(threads)
    .where(eq(threads.documentId, documentId))
    .limit(1)

  return rows.length > 0
}

export async function countMessagesForThread(db: Db, threadId: number) {
  const rows = await db
    .select({ id: messages.id })
    .from(messages)
    .where(eq(messages.threadId, threadId))

  return rows.length
}

export async function getOwnedDocument(
  db: Db,
  documentId: number,
  userId: number,
) {
  const [doc] = await db
    .select()
    .from(documents)
    .where(and(eq(documents.id, documentId), eq(documents.userId, userId)))
    .limit(1)

  return doc ?? null
}
