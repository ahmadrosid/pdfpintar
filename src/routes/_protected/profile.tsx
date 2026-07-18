import { createFileRoute } from '@tanstack/react-router'

import { LocaleSwitcher } from '#/components/LocaleSwitcher'
import ThemeToggle from '#/components/ThemeToggle'
import { DeleteAccountForm } from '#/components/profile/DeleteAccountForm'
import { UpdatePasswordForm } from '#/components/profile/UpdatePasswordForm'
import { UpdateProfileForm } from '#/components/profile/UpdateProfileForm'
import { useI18n } from '#/lib/i18n'
import { getProfileData } from '#/server/auth'

export const Route = createFileRoute('/_protected/profile')({
  loader: () => getProfileData(),
  component: ProfilePage,
})

function ProfilePage() {
  const { t } = useI18n()
  const profile = Route.useLoaderData()

  return (
    <section className="space-y-6">
      <div>
        <p className="mb-2 text-sm font-medium text-neutral-500 dark:text-neutral-400">
          {t('Profile')}
        </p>
        <h1 className="text-3xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50">
          {t('Profile settings')}
        </h1>
      </div>

      <section className="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-950">
        <h2 className="text-lg font-medium text-neutral-900 dark:text-neutral-50">
          {t('Preferences')}
        </h2>
        <p className="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
          {t('Language and appearance for your account.')}
        </p>
        <div className="mt-4 flex flex-wrap items-center gap-6">
          <div className="space-y-2">
            <p className="text-sm font-medium text-neutral-900 dark:text-neutral-100">
              {t('Language')}
            </p>
            <LocaleSwitcher />
          </div>
          <div className="space-y-2">
            <p className="text-sm font-medium text-neutral-900 dark:text-neutral-100">
              {t('Theme')}
            </p>
            <ThemeToggle />
          </div>
        </div>
      </section>

      <UpdateProfileForm
        initialName={profile.name}
        initialEmail={profile.email}
        emailVerified={profile.emailVerified}
      />

      {profile.hasPassword ? <UpdatePasswordForm /> : null}

      <DeleteAccountForm hasPassword={profile.hasPassword} />
    </section>
  )
}
