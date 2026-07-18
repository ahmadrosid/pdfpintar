import { createServerFn } from '@tanstack/react-start'
import { getRequestHeaders } from '@tanstack/react-start/server'
import { and, desc, eq, ilike } from 'drizzle-orm'

import { getDb } from '#/db/index'
import { documents, messages, threads } from '#/db/schema'
import { auth } from '#/lib/auth/auth'
import { deleteDocumentForUser, getOwnedDocument } from '#/lib/db/documents'
import { loadDocumentPageData } from '#/lib/documents/page-data'
import {
  PENDING_OPENAI_FILE_ID,
  hasOpenAIApiKey,
  uploadPdfToOpenAI,
} from '#/lib/openai/client'
import {
  buildDocumentObjectKey,
  getPresignedPdfUrl,
  uploadPrivatePdf,
} from '#/lib/storage/s3'

import { validatePdfUpload } from '#/lib/documents/validation'
import {
  copyPublicDocument,
  listSharedDocumentsForUser,
  loadPublicDocumentPageData,
  removeDocumentShare,
  toggleDocumentShare,
} from '#/lib/documents/sharing'

export type DocumentListItem = {
  id: number
  fileName: string
  createdAt: string
}

async function requireUserId() {
  const session = await auth.api.getSession({
    headers: getRequestHeaders(),
  })

  if (!session) {
    throw new Error('Unauthorized')
  }

  const userId = Number(session.user.id)
  if (!Number.isFinite(userId)) {
    throw new Error('Unauthorized')
  }

  return userId
}

function formatCreatedAt(date: Date) {
  return new Intl.RelativeTimeFormat('en', { numeric: 'auto' }).format(
    Math.round((date.getTime() - Date.now()) / (1000 * 60 * 60 * 24)),
    'day',
  )
}

export const listDocuments = createServerFn({ method: 'GET' })
  .inputValidator((data: { search?: string }) => data ?? {})
  .handler(async ({ data }) => {
    const userId = await requireUserId()
    const search = data.search?.trim() ?? ''

    const rows = await getDb()
      .select()
      .from(documents)
      .where(
        search
          ? and(
              eq(documents.userId, userId),
              ilike(documents.fileName, `%${search}%`),
            )
          : eq(documents.userId, userId),
      )
      .orderBy(desc(documents.createdAt))

    return rows.map((row) => ({
      id: row.id,
      fileName: row.fileName,
      createdAt: formatCreatedAt(row.createdAt),
    })) satisfies DocumentListItem[]
  })

export const deleteDocument = createServerFn({ method: 'POST' })
  .inputValidator((data: { id: number }) => data)
  .handler(async ({ data }) => {
    const userId = await requireUserId()
    const result = await deleteDocumentForUser(getDb(), data.id, userId)

    if (!result.ok) {
      if (result.error === 'not_found') {
        throw new Error('Document not found')
      }
      throw new Error('Forbidden')
    }

    return { success: true as const }
  })

export const uploadDocument = createServerFn({ method: 'POST' })
  .inputValidator((data: FormData) => data)
  .handler(async ({ data }) => {
    const userId = await requireUserId()
    const file = data.get('document')

    if (!(file instanceof File)) {
      throw new Error('Document file is required')
    }

    const validationError = validatePdfUpload(file)
    if (validationError) {
      throw new Error('Only PDF files up to 10MB are allowed.')
    }

    const objectKey = buildDocumentObjectKey(file.name)
    const body = new Uint8Array(await file.arrayBuffer())

    try {
      await uploadPrivatePdf(objectKey, body)
    } catch (error) {
      console.error('S3 upload failed', error)
      throw new Error('Storage upload failed. Check S3/MinIO configuration.')
    }

    let fileId = PENDING_OPENAI_FILE_ID
    if (hasOpenAIApiKey()) {
      try {
        const openaiFile = await uploadPdfToOpenAI(file, file.name)
        fileId = openaiFile.id
      } catch (error) {
        console.error('OpenAI file upload failed', error)
        throw new Error(
          'PDF saved to storage, but OpenAI upload failed. Check OPENAI_API_KEY.',
        )
      }
    }

    const [created] = await getDb()
      .insert(documents)
      .values({
        fileId,
        fileName: file.name,
        fileSize: file.size,
        filePath: objectKey,
        userId,
      })
      .returning()

    return {
      message: 'Documents uploaded successfully',
      documents: [
        {
          id: created.id,
          fileName: created.fileName,
          fileSize: created.fileSize,
          filePath: created.filePath,
        },
      ],
    }
  })

export const getDocumentPageData = createServerFn({ method: 'GET' })
  .inputValidator((data: { documentId: number }) => data)
  .handler(async ({ data }) => {
    const userId = await requireUserId()
    const result = await loadDocumentPageData(getDb(), data.documentId, userId)

    if (!result.ok) {
      throw new Error(result.error)
    }

    const [row] = await getDb()
      .select({ filePath: documents.filePath })
      .from(documents)
      .where(eq(documents.id, data.documentId))
      .limit(1)

    if (!row?.filePath) {
      throw new Error('not_found')
    }

    const pdfUrl = await getPresignedPdfUrl(row.filePath)

    return {
      ...result.data,
      pdfUrl,
    }
  })

export const clearDocumentChat = createServerFn({ method: 'POST' })
  .inputValidator((data: { documentId: number }) => data)
  .handler(async ({ data }) => {
    const userId = await requireUserId()
    const document = await getOwnedDocument(getDb(), data.documentId, userId)

    if (!document) {
      throw new Error('not_found')
    }

    const [thread] = await getDb()
      .select()
      .from(threads)
      .where(eq(threads.documentId, document.id))
      .orderBy(desc(threads.createdAt))
      .limit(1)

    if (thread) {
      await getDb().delete(messages).where(eq(messages.threadId, thread.id))
      await getDb().delete(threads).where(eq(threads.documentId, document.id))
    }

    return { success: true as const }
  })

export const getPublicDocumentPageData = createServerFn({ method: 'GET' })
  .inputValidator((data: { token: string }) => data)
  .handler(async ({ data }) => {
    const result = await loadPublicDocumentPageData(getDb(), data.token)

    if (!result.ok) {
      throw new Error(result.error)
    }

    const [row] = await getDb()
      .select({ filePath: documents.filePath })
      .from(documents)
      .where(eq(documents.id, result.data.document.id))
      .limit(1)

    if (!row?.filePath) {
      throw new Error('not_found')
    }

    const pdfUrl = await getPresignedPdfUrl(row.filePath)

    return {
      ...result.data,
      pdfUrl,
    }
  })

export const listSharedDocuments = createServerFn({ method: 'GET' }).handler(
  async () => {
    const userId = await requireUserId()
    return listSharedDocumentsForUser(getDb(), userId)
  },
)

export const toggleShare = createServerFn({ method: 'POST' })
  .inputValidator((data: { documentId: number }) => data)
  .handler(async ({ data }) => {
    const userId = await requireUserId()
    const result = await toggleDocumentShare(
      getDb(),
      data.documentId,
      userId,
    )

    if (!result.ok) {
      throw new Error(result.error)
    }

    return {
      isPublic: result.document.isPublic,
      sharingToken: result.document.sharingToken,
      message: result.message,
    }
  })

export const removeShare = createServerFn({ method: 'POST' })
  .inputValidator((data: { sharingToken: string }) => data)
  .handler(async ({ data }) => {
    const userId = await requireUserId()
    const result = await removeDocumentShare(
      getDb(),
      data.sharingToken,
      userId,
    )

    if (!result.ok) {
      throw new Error(result.error)
    }

    return { success: true as const }
  })

export const copyDocument = createServerFn({ method: 'POST' })
  .inputValidator((data: { documentId: number }) => data)
  .handler(async ({ data }) => {
    const userId = await requireUserId()
    const result = await copyPublicDocument(getDb(), data.documentId, userId)

    if (!result.ok) {
      throw new Error(result.error)
    }

    return { documentId: result.documentId }
  })
