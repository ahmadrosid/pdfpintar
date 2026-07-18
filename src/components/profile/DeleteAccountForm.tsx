import { useState } from 'react'
import { useRouter } from '@tanstack/react-router'

import { authClient } from '#/lib/auth/auth-client'
import { useI18n } from '#/lib/i18n'
import { deleteAccount } from '#/server/auth'

type DeleteAccountFormProps = {
  hasPassword: boolean
}

export function DeleteAccountForm({ hasPassword }: DeleteAccountFormProps) {
  const { t } = useI18n()
  const router = useRouter()
  const [open, setOpen] = useState(false)
  const [password, setPassword] = useState('')
  const [error, setError] = useState<string | null>(null)
  const [pending, setPending] = useState(false)

  async function handleDelete(event: React.FormEvent) {
    event.preventDefault()
    setPending(true)
    setError(null)

    try {
      if (hasPassword) {
        await deleteAccount({ data: { password } })
      } else {
        const { error: deleteError } = await authClient.deleteUser()
        if (deleteError) {
          throw deleteError
        }
      }

      await authClient.signOut()
      await router.navigate({ to: '/' })
    } catch {
      setError(
        hasPassword
          ? t('Current password is incorrect.')
          : t('Account deletion failed.'),
      )
    } finally {
      setPending(false)
    }
  }

  return (
    <section className="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-950">
      <header className="mb-6">
        <h2 className="text-lg font-medium text-[var(--sea-ink)]">
          {t('Delete Account')}
        </h2>
        <p className="mt-1 text-sm text-[var(--sea-ink-soft)]">
          {t(
            'Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.',
          )}
        </p>
      </header>

      <button
        type="button"
        className="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
        onClick={() => setOpen(true)}
      >
        {t('Delete Account')}
      </button>

      {open ? (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
          <form
            onSubmit={handleDelete}
            className="w-full max-w-lg rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-950"
          >
            <h3 className="mb-2 text-lg font-semibold text-[var(--sea-ink)]">
              {t('Are you sure you want to delete your account?')}
            </h3>
            <p className="mb-6 text-sm text-[var(--sea-ink-soft)]">
              {hasPassword
                ? t(
                    'Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.',
                  )
                : t(
                    'Once your account is deleted, all of its resources and data will be permanently deleted.',
                  )}
            </p>

            {hasPassword ? (
              <label className="mb-4 block text-sm">
                <span className="mb-1 block font-medium text-[var(--sea-ink)]">
                  {t('Password')}
                </span>
                <input
                  type="password"
                  required
                  autoComplete="current-password"
                  value={password}
                  onChange={(event) => setPassword(event.target.value)}
                  className="demo-input w-full"
                />
              </label>
            ) : null}

            {error ? (
              <p className="mb-4 text-sm text-red-600" role="alert">
                {error}
              </p>
            ) : null}

            <div className="flex justify-end gap-3">
              <button
                type="button"
                className="rounded-lg px-4 py-2 text-sm font-semibold text-neutral-500 hover:bg-neutral-50 dark:text-neutral-400 dark:hover:bg-neutral-900"
                onClick={() => {
                  setOpen(false)
                  setPassword('')
                  setError(null)
                }}
              >
                {t('Cancel')}
              </button>
              <button
                type="submit"
                disabled={pending}
                className="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-60"
              >
                {pending ? t('Deleting...') : t('Delete Account')}
              </button>
            </div>
          </form>
        </div>
      ) : null}
    </section>
  )
}
