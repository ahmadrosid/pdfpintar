export type ChatEventType = 'thread' | 'token' | 'notice'

export type ChatNotice = {
  level: 'error'
  message: string
}

export function formatSseEvent(type: ChatEventType, data: unknown) {
  return `data: ${JSON.stringify({ type, data })}\n\n`
}

export function stripCitationMarkers(text: string) {
  return text.replace(/【\d+:\d+†.*?】/g, '')
}
