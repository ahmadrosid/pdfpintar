import { useEffect, useId, useRef, useState } from 'react'
import { createFileRoute, Link } from '@tanstack/react-router'

import { useI18n } from '#/lib/i18n'
import { Route as rootRoute } from '#/routes/__root'

const PRODUCT_PREVIEW_URL =
  'https://res.cloudinary.com/dr15yjl8w/image/upload/v1722770981/public/e6w5shtwz1thgozzsoxb.png'

const DEMO_VIDEO_URL =
  'https://www.youtube.com/embed/2AR02P5-RQk?si=QfX2QzGGt3_ZZHW-'

export const Route = createFileRoute('/')({
  component: LandingPage,
})

function LandingPage() {
  const { t } = useI18n()
  const { session } = rootRoute.useRouteContext()
  const [demoOpen, setDemoOpen] = useState(false)

  return (
    <main className="landing-page relative min-h-screen overflow-x-hidden bg-white">
      <div className="mx-auto min-h-screen max-w-6xl">
        <header className="flex items-center justify-between px-6 py-5 sm:px-10 sm:py-7">
          <Link
            to="/"
            className="text-sm font-extrabold tracking-[0.14em] text-neutral-900 no-underline sm:text-base"
          >
            PDFPINTAR
          </Link>
          <nav className="flex items-center gap-1 sm:gap-2">
            {session ? (
              <Link
                to="/dashboard"
                className="rounded-md px-3 py-2 text-sm font-medium text-neutral-600 no-underline transition hover:text-neutral-900"
              >
                {t('Dashboard')}
              </Link>
            ) : (
              <>
                <Link
                  to="/login"
                  search={{ redirect: undefined }}
                  className="rounded-md px-3 py-2 text-sm font-medium text-neutral-600 no-underline transition hover:text-neutral-900"
                >
                  {t('Log in')}
                </Link>
                <Link
                  to="/register"
                  className="rounded-md px-3 py-2 text-sm font-medium text-neutral-600 no-underline transition hover:text-neutral-900"
                >
                  {t('Register')}
                </Link>
              </>
            )}
          </nav>
        </header>

        <section className="landing-hero mx-auto max-w-3xl px-6 pb-6 pt-4 text-center sm:px-10 sm:pb-8 sm:pt-6">
          <h1 className="landing-fade text-balance text-3xl font-bold tracking-tight text-neutral-900 sm:text-5xl sm:leading-[1.15]">
            {t('Stop reading PDFs. Start asking.')}
          </h1>
          <p className="landing-fade landing-fade-delay-1 mx-auto mt-3 max-w-xl text-base text-neutral-500 sm:mt-4 sm:text-lg">
            {t('Chat with your PDFs. Get answers instantly.')}
          </p>
          <div className="landing-fade landing-fade-delay-2 mt-6 flex flex-wrap items-center justify-center gap-4 sm:mt-8">
            {session ? (
              <Link
                to="/dashboard"
                className="inline-flex items-center justify-center rounded-lg bg-neutral-900 px-5 py-2.5 text-sm font-semibold tracking-wide text-white! no-underline transition hover:-translate-y-0.5 hover:bg-black hover:text-white!"
              >
                {t('Dashboard')}
              </Link>
            ) : (
              <Link
                to="/register"
                className="inline-flex items-center justify-center rounded-lg bg-neutral-900 px-5 py-2.5 text-sm font-semibold tracking-wide text-white! no-underline transition hover:-translate-y-0.5 hover:bg-black hover:text-white!"
              >
                {t('TRY FOR FREE')}
              </Link>
            )}
            <button
              type="button"
              onClick={() => setDemoOpen(true)}
              className="border-b-2 border-transparent text-sm font-medium text-neutral-700 transition hover:border-neutral-900 hover:text-neutral-900"
            >
              {t('Watch Demo')}
            </button>
          </div>
        </section>

        <div className="landing-fade landing-fade-delay-3 px-4 pb-4 sm:px-8 sm:pb-8">
          <div className="overflow-hidden rounded-2xl bg-neutral-700 p-1.5 shadow-2xl sm:rounded-3xl sm:p-2">
            <img
              src={PRODUCT_PREVIEW_URL}
              alt={t('PDFPintar product preview')}
              className="block w-full rounded-xl sm:rounded-2xl"
              width={1600}
              height={900}
              loading="eager"
              decoding="async"
            />
          </div>
        </div>
      </div>

      <DemoModal open={demoOpen} onClose={() => setDemoOpen(false)} />
    </main>
  )
}

function DemoModal({
  open,
  onClose,
}: {
  open: boolean
  onClose: () => void
}) {
  const { t } = useI18n()
  const titleId = useId()
  const closeRef = useRef<HTMLButtonElement>(null)

  useEffect(() => {
    if (!open) return

    const previousOverflow = document.body.style.overflow
    document.body.style.overflow = 'hidden'
    closeRef.current?.focus()

    function onKeyDown(event: KeyboardEvent) {
      if (event.key === 'Escape') onClose()
    }

    window.addEventListener('keydown', onKeyDown)
    return () => {
      document.body.style.overflow = previousOverflow
      window.removeEventListener('keydown', onKeyDown)
    }
  }, [open, onClose])

  if (!open) return null

  return (
    <div
      className="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-[2px]"
      role="presentation"
      onClick={onClose}
    >
      <div
        role="dialog"
        aria-modal="true"
        aria-labelledby={titleId}
        className="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl"
        onClick={(event) => event.stopPropagation()}
      >
        <div className="flex items-center justify-between border-b border-neutral-200 px-5 py-4">
          <h2 id={titleId} className="m-0 text-lg font-bold text-neutral-900">
            {t('Watch Demo')}
          </h2>
          <button
            ref={closeRef}
            type="button"
            onClick={onClose}
            className="rounded-md px-2 py-1 text-sm font-medium text-neutral-500 transition hover:bg-neutral-100 hover:text-neutral-900"
          >
            {t('Close')}
          </button>
        </div>
        <div className="px-5 py-4">
          <div className="aspect-video overflow-hidden rounded-md bg-neutral-900">
            <iframe
              className="h-full w-full"
              src={DEMO_VIDEO_URL}
              title={t('Watch Demo')}
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowFullScreen
            />
          </div>
          <div className="mt-4 flex justify-end gap-2">
            <Link
              to="/register"
              className="inline-flex items-center justify-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white! no-underline transition hover:bg-black hover:text-white!"
              onClick={onClose}
            >
              {t('TRY FOR FREE')}
            </Link>
            <button
              type="button"
              onClick={onClose}
              className="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50"
            >
              {t('Close')}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}
