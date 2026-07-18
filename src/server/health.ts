import { createServerFn } from '@tanstack/react-start'

export function getHealthStatus() {
  return {
    status: 'ok' as const,
    app: 'pdfpintar',
    stack: 'tanstack-start',
  }
}

export const healthCheck = createServerFn({ method: 'GET' }).handler(
  async () => getHealthStatus(),
)
