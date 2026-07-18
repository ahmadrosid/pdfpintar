import { useState } from 'react'

import { validatePasswordChange } from '#/lib/auth/profile'
import { useI18n, type MessageKey } from '#/lib/i18n'
import { changePassword } from '#/server/auth'

export function UpdatePasswordForm() {
  const { t } = useI18n()
  const [currentPassword, setCurrentPassword] = useState('')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  const [error, setError] = useState<string | null>(null)
  const [saved, setSaved] = useState(false)
  const [pending, setPending] = useState(false)

  async function onSubmit(event: React.FormEvent) {
    event.preventDefault()
    setError(null)
    setSaved(false)

    const validationError = validatePasswordChange({
      currentPassword,
      password,
      passwordConfirmation,
    })

    if (validationError) {
      setError(t(passwordErrorKey(validationError)))
      return
    }

    setPending(true)

    try {
      await changePassword({
        data: {
          currentPassword,
          newPassword: password,
        },
      })
      setCurrentPassword('')
      setPassword('')
      setPasswordConfirmation('')
      setSaved(true)
    } catch {
      setError(t('Current password is incorrect.'))
    } finally {
      setPending(false)
    }
  }

  return (
    <section className="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-950">
      <header className="mb-6">
        <h2 className="text-lg font-medium text-[var(--sea-ink)]">
          {t('Update Password')}
        </h2>
        <p className="mt-1 text-sm text-[var(--sea-ink-soft)]">
          {t('Ensure your account is using a long, random password to stay secure.')}
        </p>
      </header>

      <form onSubmit={onSubmit} className="space-y-4">
        <label className="block text-sm">
          <span className="mb-1 block font-medium text-[var(--sea-ink)]">
            {t('Current Password')}
          </span>
          <input
            type="password"
            autoComplete="current-password"
            value={currentPassword}
            onChange={(event) => setCurrentPassword(event.target.value)}
            className="demo-input w-full"
          />
        </label>

        <label className="block text-sm">
          <span className="mb-1 block font-medium text-[var(--sea-ink)]">
            {t('New Password')}
          </span>
          <input
            type="password"
            autoComplete="new-password"
            value={password}
            onChange={(event) => setPassword(event.target.value)}
            className="demo-input w-full"
          />
        </label>

        <label className="block text-sm">
          <span className="mb-1 block font-medium text-[var(--sea-ink)]">
            {t('Confirm Password')}
          </span>
          <input
            type="password"
            autoComplete="new-password"
            value={passwordConfirmation}
            onChange={(event) => setPasswordConfirmation(event.target.value)}
            className="demo-input w-full"
          />
        </label>

        {error ? (
          <p className="text-sm text-red-600" role="alert">
            {error}
          </p>
        ) : null}

        <div className="flex items-center gap-4">
          <button type="submit" disabled={pending} className="demo-button">
            {pending ? t('Saving...') : t('Save')}
          </button>
          {saved ? (
            <p className="text-sm text-green-600">{t('Saved.')}</p>
          ) : null}
        </div>
      </form>
    </section>
  )
}

function passwordErrorKey(code: string): MessageKey {
  switch (code) {
    case 'current_password_required':
      return 'Current password is required.'
    case 'password_too_short':
      return 'Password must be at least 8 characters.'
    case 'password_mismatch':
      return 'Password confirmation does not match.'
    default:
      return 'Password update failed.'
  }
}
