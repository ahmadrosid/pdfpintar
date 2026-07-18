import type OpenAI from 'openai'

export async function createDocumentAssistant(
  openai: OpenAI,
  document: { fileName: string; fileId: string },
) {
  return openai.beta.assistants.create({
    name: document.fileName,
    tools: [{ type: 'file_search' }],
    tool_resources: {
      file_search: {
        vector_stores: [
          {
            file_ids: [document.fileId],
          },
        ],
      },
    },
    instructions:
      "Your are a helpful assistant. Use the provided document to answer the user's question.",
    model: 'gpt-4o-mini',
  })
}
