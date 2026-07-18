import { createContext, useContext, useMemo } from 'react'

import { en, type MessageKey } from '#/lib/i18n/en'
import { id } from '#/lib/i18n/id'

export type Locale = 'en' | 'id'

const dictionaries = { en, id }

type I18nContextValue = {
  locale: Locale
  t: (key: MessageKey) => string
}

const I18nContext = createContext<I18nContextValue | null>(null)

export function I18nProvider({
  locale,
  children,
}: {
  locale: Locale
  children: React.ReactNode
}) {
  const value = useMemo<I18nContextValue>(() => {
    const dictionary = dictionaries[locale] ?? en
    return {
      locale,
      t: (key) => dictionary[key] ?? en[key] ?? key,
    }
  }, [locale])

  return <I18nContext.Provider value={value}>{children}</I18nContext.Provider>
}

export function useI18n() {
  const context = useContext(I18nContext)
  if (!context) {
    return {
      locale: 'en' as Locale,
      t: (key: MessageKey) => en[key] ?? key,
    }
  }
  return context
}

export function resolveLocale(value: string | null | undefined): Locale {
  return value === 'id' ? 'id' : 'en'
}
