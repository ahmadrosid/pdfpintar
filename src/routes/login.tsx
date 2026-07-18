import { Link, createFileRoute, redirect } from '@tanstack/react-router'
import { useState } from 'react'

import { authClient } from '#/lib/auth/auth-client'
import { getSession } from '#/lib/auth/auth-functions'

export const Route = createFileRoute('/login')({
  validateSearch: (search: Record<string, unknown>) => ({
    redirect: typeof search.redirect === 'string' ? search.redirect : undefined,
  }),
  beforeLoad: async () => {
    const session = await getSession()
    if (session) {
      throw redirect({ to: '/dashboard' })
    }
  },
  component: LoginPage,
})

function LoginPage() {
  const { redirect: redirectTo } = Route.useSearch()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState<string | null>(null)
  const [pending, setPending] = useState(false)

  async function onSubmit(event: React.FormEvent) {
    event.preventDefault()
    setPending(true)
    setError(null)

    const { error: signInError } = await authClient.signIn.email({
      email,
      password,
    })

    setPending(false)

    if (signInError) {
      setError(signInError.message ?? 'Login failed')
      return
    }

    window.location.href = redirectTo ?? '/dashboard'
  }

  async function onGoogleSignIn() {
    await authClient.signIn.social({
      provider: 'google',
      callbackURL: redirectTo ?? '/dashboard',
    })
  }

  return (
    <main className="page-wrap flex min-h-[70vh] items-center justify-center px-4 py-12">
      <section className="w-full max-w-md rounded-xl border border-neutral-200 bg-white p-6 sm:p-8 dark:border-neutral-800 dark:bg-neutral-950">
        <h1 className="mb-2 text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50">
          Log in
        </h1>
        <p className="mb-6 text-sm text-neutral-500 dark:text-neutral-400">
          Access your PDF library and chat history.
        </p>

        <button
          type="button"
          onClick={onGoogleSignIn}
          className="mb-4 w-full rounded-lg border border-neutral-200 bg-white px-4 py-2.5 text-sm font-semibold text-neutral-900 transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-50 dark:hover:bg-neutral-800"
        >
          Continue with Google
        </button>

        <p className="mb-4 text-center text-xs text-neutral-500 dark:text-neutral-400">
          Or use email and password
        </p>

        <form onSubmit={onSubmit} className="space-y-4">
          <label className="block text-sm">
            <span className="mb-1 block font-medium text-neutral-900 dark:text-neutral-100">
              Email
            </span>
            <input
              type="email"
              required
              autoComplete="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="demo-input w-full"
            />
          </label>
          <label className="block text-sm">
            <span className="mb-1 block font-medium text-neutral-900 dark:text-neutral-100">
              Password
            </span>
            <input
              type="password"
              required
              autoComplete="current-password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="demo-input w-full"
            />
          </label>
          {error ? (
            <p className="text-sm text-red-600" role="alert">
              {error}
            </p>
          ) : null}
          <button
            type="submit"
            disabled={pending}
            className="demo-button w-full disabled:opacity-60"
          >
            {pending ? 'Signing in…' : 'Sign in'}
          </button>
        </form>

        <p className="mt-6 text-center text-sm text-neutral-500 dark:text-neutral-400">
          No account?{' '}
          <Link
            to="/register"
            className="font-semibold text-neutral-900 no-underline hover:underline dark:text-neutral-100"
          >
            Register
          </Link>
        </p>
      </section>
    </main>
  )
}
