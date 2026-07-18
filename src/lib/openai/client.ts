import OpenAI from 'openai'

let client: OpenAI | undefined

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

export async function uploadPdfToOpenAI(file: File, filename: string) {
  const openai = getOpenAIClient()
  let name = filename
  if (!name.toLowerCase().endsWith('.pdf')) {
    name += '.pdf'
  }

  return openai.files.create({
    file: await OpenAI.toFile(await file.arrayBuffer(), name, {
      type: 'application/pdf',
    }),
    purpose: 'assistants',
  })
}
