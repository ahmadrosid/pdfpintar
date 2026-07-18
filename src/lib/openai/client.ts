import OpenAI from 'openai'

export const PENDING_OPENAI_FILE_ID = 'pending'

let client: OpenAI | undefined

export function hasOpenAIApiKey() {
  return Boolean(process.env.OPENAI_API_KEY?.trim())
}

export function getOpenAIClient() {
  if (!client) {
    const apiKey = process.env.OPENAI_API_KEY
    if (!apiKey) {
      throw new Error('OPENAI_API_KEY is not set')
    }

    client = new OpenAI({
      apiKey,
      organization: process.env.OPENAI_ORGANIZATION || undefined,
    })
  }

  return client
}

export function isPendingOpenAIFileId(fileId: string | null | undefined) {
  return !fileId || fileId === PENDING_OPENAI_FILE_ID
}

export async function uploadPdfBytesToOpenAI(
  body: Uint8Array,
  filename: string,
) {
  const openai = getOpenAIClient()
  let name = filename
  if (!name.toLowerCase().endsWith('.pdf')) {
    name += '.pdf'
  }

  return openai.files.create({
    file: await OpenAI.toFile(body, name, {
      type: 'application/pdf',
    }),
    purpose: 'assistants',
  })
}

export async function uploadPdfToOpenAI(file: File, filename: string) {
  return uploadPdfBytesToOpenAI(new Uint8Array(await file.arrayBuffer()), filename)
}
