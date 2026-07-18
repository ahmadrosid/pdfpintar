import { createServerFn } from '@tanstack/react-start'
import { getCookie, setCookie } from '@tanstack/react-start/server'

import { resolveLocale, type Locale } from '#/lib/i18n'

export const getLocale = createServerFn({ method: 'GET' }).handler(async () => {
  return resolveLocale(getCookie('locale'))
})

export const setLocale = createServerFn({ method: 'POST' })
  .inputValidator((data: { locale: Locale }) => data)
  .handler(async ({ data }) => {
    setCookie('locale', data.locale, {
      path: '/',
      maxAge: 60 * 60 * 24 * 365,
      sameSite: 'lax',
    })
    return data.locale
  })
