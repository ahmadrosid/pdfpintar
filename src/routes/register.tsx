import { Link, createFileRoute, redirect } from '@tanstack/react-router'
import { useState } from 'react'

import { authClient } from '#/lib/auth/auth-client'
import { getSession } from '#/lib/auth/auth-functions'
import { useI18n } from '#/lib/i18n'

export const Route = createFileRoute('/register')({
  beforeLoad: async () => {
    const session = await getSession()
    if (session) {
      throw redirect({ to: '/dashboard' })
    }
  },
  component: RegisterPage,
})

function RegisterPage() {
  const { t } = useI18n()
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [confirmPassword, setConfirmPassword] = useState('')
  const [error, setError] = useState<string | null>(null)
  const [pending, setPending] = useState(false)

  async function onSubmit(event: React.FormEvent) {
    event.preventDefault()
    setError(null)

    if (password !== confirmPassword) {
      setError(t('Password confirmation does not match.'))
      return
    }

    setPending(true)

    const { error: signUpError } = await authClient.signUp.email({
      name,
      email,
      password,
    })

    setPending(false)

    if (signUpError) {
      setError(signUpError.message ?? 'Registration failed')
      return
    }

    window.location.href = '/dashboard'
  }

  return (
    <main className="page-wrap flex min-h-[70vh] items-center justify-center px-4 py-12">
      <section className="w-full max-w-md rounded-xl border border-neutral-200 bg-white p-6 sm:p-8 dark:border-neutral-800 dark:bg-neutral-950">
        <h1 className="mb-2 text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50">
          Create account
        </h1>
        <p className="mb-6 text-sm text-neutral-500 dark:text-neutral-400">
          Start chatting with your PDF documents.
        </p>

        <form onSubmit={onSubmit} className="space-y-4">
          <label className="block text-sm">
            <span className="mb-1 block font-medium text-neutral-900 dark:text-neutral-100">
              Name
            </span>
            <input
              type="text"
              required
              autoComplete="name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              className="demo-input w-full"
            />
          </label>
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
              minLength={8}
              autoComplete="new-password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="demo-input w-full"
            />
          </label>
          <label className="block text-sm">
            <span className="mb-1 block font-medium text-neutral-900 dark:text-neutral-100">
              {t('Confirm Password')}
            </span>
            <input
              type="password"
              required
              minLength={8}
              autoComplete="new-password"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
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
            {pending ? 'Creating account…' : 'Register'}
          </button>
        </form>

        <p className="mt-6 text-center text-sm text-neutral-500 dark:text-neutral-400">
          Already have an account?{' '}
          <Link
            to="/login"
            search={{ redirect: undefined }}
            className="font-semibold text-neutral-900 no-underline hover:underline dark:text-neutral-100"
          >
            Log in
          </Link>
        </p>
      </section>
    </main>
  )
}
