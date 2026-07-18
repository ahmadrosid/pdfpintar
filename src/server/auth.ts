import { createServerFn } from '@tanstack/react-start'
import { getRequestHeaders } from '@tanstack/react-start/server'
import { eq } from 'drizzle-orm'

import { getDb } from '#/db/index'
import { accounts } from '#/db/schema'
import { auth } from '#/lib/auth/auth'
import { updateUserProfile } from '#/lib/auth/profile'

async function requireSession() {
  const session = await auth.api.getSession({
    headers: getRequestHeaders(),
  })

  if (!session) {
    throw new Error('Unauthorized')
  }

  const userId = Number(session.user.id)
  if (!Number.isFinite(userId)) {
    throw new Error('Unauthorized')
  }

  return { session, userId }
}

export const getProfileData = createServerFn({ method: 'GET' }).handler(
  async () => {
    const { session, userId } = await requireSession()

    const userAccounts = await getDb()
      .select({
        providerId: accounts.providerId,
        password: accounts.password,
      })
      .from(accounts)
      .where(eq(accounts.userId, userId))

    const hasPassword = userAccounts.some(
      (account) =>
        account.providerId === 'credential' && Boolean(account.password),
    )

    return {
      name: session.user.name,
      email: session.user.email,
      emailVerified: session.user.emailVerified,
      hasPassword,
    }
  },
)

export const updateProfile = createServerFn({ method: 'POST' })
  .inputValidator((data: { name: string; email: string }) => data)
  .handler(async ({ data }) => {
    const { userId } = await requireSession()
    const result = await updateUserProfile(getDb(), userId, data)

    if (!result.ok) {
      throw new Error(result.error)
    }

    return {
      name: result.user.name,
      email: result.user.email,
      emailVerified: result.user.emailVerified,
    }
  })

export const changePassword = createServerFn({ method: 'POST' })
  .inputValidator(
    (data: {
      currentPassword: string
      newPassword: string
      revokeOtherSessions?: boolean
    }) => data,
  )
  .handler(async ({ data }) => {
    const headers = getRequestHeaders()
    const session = await auth.api.getSession({ headers })

    if (!session) {
      throw new Error('Unauthorized')
    }

    await auth.api.changePassword({
      body: {
        currentPassword: data.currentPassword,
        newPassword: data.newPassword,
        revokeOtherSessions: data.revokeOtherSessions ?? true,
      },
      headers,
    })

    return { success: true as const }
  })

export const deleteAccount = createServerFn({ method: 'POST' })
  .inputValidator((data: { password?: string }) => data)
  .handler(async ({ data }) => {
    const headers = getRequestHeaders()
    const session = await auth.api.getSession({ headers })

    if (!session) {
      throw new Error('Unauthorized')
    }

    await auth.api.deleteUser({
      body: {
        password: data.password,
      },
      headers,
    })

    return { success: true as const }
  })
