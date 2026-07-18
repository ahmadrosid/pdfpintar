import { useRouter } from '@tanstack/react-router'
import { useTransition } from 'react'

import { useI18n, type Locale } from '#/lib/i18n'
import { setLocale } from '#/server/locale'

export function LocaleSwitcher() {
  const { locale, t } = useI18n()
  const router = useRouter()
  const [pending, startTransition] = useTransition()

  function changeLocale(next: Locale) {
    if (next === locale) return

    startTransition(async () => {
      await setLocale({ data: { locale: next } })
      await router.invalidate()
    })
  }

  return (
    <div className="flex items-center gap-0.5 rounded-md border border-neutral-200 p-0.5 text-xs font-medium dark:border-neutral-700">
      <span className="sr-only">{t('Language')}</span>
      {(['en', 'id'] as const).map((code) => (
        <button
          key={code}
          type="button"
          disabled={pending}
          onClick={() => changeLocale(code)}
          className={`rounded px-2.5 py-1 transition ${
            locale === code
              ? 'bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900'
              : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'
          }`}
        >
          {code === 'en' ? 'EN' : 'ID'}
        </button>
      ))}
    </div>
  )
}
