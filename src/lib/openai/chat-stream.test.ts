import { describe, expect, it } from 'vitest'

import {
  formatSseEvent,
  stripCitationMarkers,
} from '#/lib/openai/chat-events'

describe('chat stream helpers', () => {
  it('formats SSE events as JSON lines', () => {
    expect(formatSseEvent('token', 'hello')).toBe(
      'data: {"type":"token","data":"hello"}\n\n',
    )
  })

  it('strips OpenAI citation markers from streamed text', () => {
    expect(
      stripCitationMarkers('Answer【4:12†source.pdf】 with citation'),
    ).toBe('Answer with citation')
  })
})
