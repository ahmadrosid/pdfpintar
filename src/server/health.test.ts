import { describe, expect, it } from 'vitest'
import { getHealthStatus } from '#/server/health'

describe('getHealthStatus', () => {
  it('returns ok status', () => {
    expect(getHealthStatus()).toEqual({
      status: 'ok',
      app: 'pdfpintar',
      stack: 'tanstack-start',
    })
  })
})
