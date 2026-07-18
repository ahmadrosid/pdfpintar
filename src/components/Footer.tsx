import { useRouterState } from '@tanstack/react-router'

import { LocaleSwitcher } from '#/components/LocaleSwitcher'
import ThemeToggle from '#/components/ThemeToggle'

export default function Footer() {
  const pathname = useRouterState({ select: (s) => s.location.pathname })
  const year = new Date().getFullYear()

  if (pathname === '/') return null

  return (
    <footer className="mt-16 border-t border-neutral-200 px-4 py-10 text-neutral-500 dark:border-neutral-800 dark:text-neutral-400">
      <div className="page-wrap flex flex-col items-center justify-between gap-4 sm:flex-row">
        <p className="m-0 text-center text-sm sm:text-left">
          &copy; {year} PDFPINTAR. All rights reserved.
        </p>
        <div className="flex items-center gap-2">
          <LocaleSwitcher />
          <ThemeToggle />
        </div>
      </div>
    </footer>
  )
}
