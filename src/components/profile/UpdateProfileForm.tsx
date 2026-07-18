import { useState } from 'react'
import { useRouter } from '@tanstack/react-router'

import { useI18n, type MessageKey } from '#/lib/i18n'
import { updateProfile } from '#/server/auth'

type UpdateProfileFormProps = {
  initialName: string
  initialEmail: string
  emailVerified: boolean
}

export function UpdateProfileForm({
  initialName,
  initialEmail,
  emailVerified,
}: UpdateProfileFormProps) {
  const { t } = useI18n()
  const router = useRouter()
  const [name, setName] = useState(initialName)
  const [email, setEmail] = useState(initialEmail)
  const [error, setError] = useState<string | null>(null)
  const [saved, setSaved] = useState(false)
  const [pending, setPending] = useState(false)

  async function onSubmit(event: React.FormEvent) {
    event.preventDefault()
    setPending(true)
    setError(null)
    setSaved(false)

    try {
      await updateProfile({ data: { name, email } })
      setSaved(true)
      await router.invalidate()
    } catch (caught) {
      const message =
        caught instanceof Error ? caught.message : 'profile_update_failed'
      setError(t(profileErrorKey(message)))
    } finally {
      setPending(false)
    }
  }

  return (
    <section className="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-950">
      <header className="mb-6">
        <h2 className="text-lg font-medium text-[var(--sea-ink)]">
          {t('Profile Information')}
        </h2>
        <p className="mt-1 text-sm text-[var(--sea-ink-soft)]">
          {t("Update your account's profile information and email address.")}
        </p>
      </header>

      <form onSubmit={onSubmit} className="space-y-4">
        <label className="block text-sm">
          <span className="mb-1 block font-medium text-[var(--sea-ink)]">
            {t('Name')}
          </span>
          <input
            type="text"
            required
            autoComplete="name"
            value={name}
            onChange={(event) => setName(event.target.value)}
            className="demo-input w-full"
          />
        </label>

        <label className="block text-sm">
          <span className="mb-1 block font-medium text-[var(--sea-ink)]">
            {t('Email')}
          </span>
          <input
            type="email"
            required
            autoComplete="username"
            value={email}
            onChange={(event) => setEmail(event.target.value)}
            className="demo-input w-full"
          />
        </label>

        {!emailVerified ? (
          <p className="text-sm text-[var(--sea-ink-soft)]">
            {t('Your email address is unverified.')}
          </p>
        ) : null}

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

function profileErrorKey(code: string): MessageKey {
  switch (code) {
    case 'email_taken':
      return 'This email is already in use.'
    case 'invalid_email':
      return 'Please enter a valid email address.'
    case 'name_required':
      return 'Name is required.'
    default:
      return 'Profile update failed.'
  }
}
