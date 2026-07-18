import { Link, useRouter, useRouterState } from '@tanstack/react-router'

import { authClient } from '#/lib/auth/auth-client'
import { useI18n } from '#/lib/i18n'
import { Route as rootRoute } from '#/routes/__root'

export default function Header() {
  const { t } = useI18n()
  const router = useRouter()
  const { session } = rootRoute.useRouteContext()
  const pathname = useRouterState({ select: (s) => s.location.pathname })

  async function handleLogout() {
    await authClient.signOut()
    await router.invalidate()
    await router.navigate({ to: '/' })
  }

  if (pathname === '/') return null

  return (
    <header className="sticky top-0 z-50 border-b border-neutral-200 bg-white/95 px-4 backdrop-blur-sm dark:border-neutral-800 dark:bg-neutral-950/95">
      <nav className="page-wrap flex flex-wrap items-center gap-x-2 gap-y-2 py-4 sm:py-5">
        <Link
          to="/"
          className="mr-2 text-sm font-extrabold tracking-[0.14em] text-neutral-900 no-underline sm:text-base dark:text-neutral-50"
        >
          PDFPINTAR
        </Link>

        <div className="flex flex-wrap items-center gap-x-1 gap-y-1 text-sm">
          {session ? (
            <>
              <Link
                to="/dashboard"
                className="nav-link"
                activeProps={{ className: 'nav-link is-active' }}
              >
                {t('Documents')}
              </Link>
              <Link
                to="/shared-documents"
                className="nav-link"
                activeProps={{ className: 'nav-link is-active' }}
              >
                {t('Shared')}
              </Link>
              <Link
                to="/pdf-generator"
                className="nav-link"
                activeProps={{ className: 'nav-link is-active' }}
              >
                {t('PDF Generator')}
              </Link>
              <Link
                to="/profile"
                className="nav-link"
                activeProps={{ className: 'nav-link is-active' }}
              >
                {t('Profile')}
              </Link>
            </>
          ) : (
            <>
              <Link
                to="/login"
                search={{ redirect: undefined }}
                className="nav-link"
                activeProps={{ className: 'nav-link is-active' }}
              >
                {t('Log in')}
              </Link>
              <Link
                to="/register"
                className="nav-link"
                activeProps={{ className: 'nav-link is-active' }}
              >
                {t('Register')}
              </Link>
            </>
          )}
        </div>

        {session ? (
          <div className="ml-auto">
            <button
              type="button"
              onClick={handleLogout}
              className="rounded-lg px-3 py-1.5 text-sm font-medium text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-neutral-50"
            >
              {t('Log Out')}
            </button>
          </div>
        ) : null}
      </nav>
    </header>
  )
}
