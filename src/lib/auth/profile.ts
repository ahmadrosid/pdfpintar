import { and, eq, ne } from 'drizzle-orm'
import type { NodePgDatabase } from 'drizzle-orm/node-postgres'

import * as schema from '#/db/schema'
import { users } from '#/db/schema'

type Db = NodePgDatabase<typeof schema>

export type ProfileInput = {
  name: string
  email: string
}

export function validateProfileInput(input: ProfileInput) {
  const name = input.name.trim()
  const email = input.email.trim().toLowerCase()

  if (!name) {
    return 'name_required' as const
  }

  if (name.length > 255) {
    return 'name_too_long' as const
  }

  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    return 'invalid_email' as const
  }

  return null
}

export function validatePasswordChange(input: {
  currentPassword: string
  password: string
  passwordConfirmation: string
}) {
  if (!input.currentPassword) {
    return 'current_password_required' as const
  }

  if (input.password.length < 8) {
    return 'password_too_short' as const
  }

  if (input.password !== input.passwordConfirmation) {
    return 'password_mismatch' as const
  }

  return null
}

export async function updateUserProfile(
  db: Db,
  userId: number,
  input: ProfileInput,
) {
  const validationError = validateProfileInput(input)
  if (validationError) {
    return { ok: false as const, error: validationError }
  }

  const name = input.name.trim()
  const email = input.email.trim().toLowerCase()

  const [current] = await db
    .select()
    .from(users)
    .where(eq(users.id, userId))
    .limit(1)

  if (!current) {
    return { ok: false as const, error: 'not_found' as const }
  }

  const [existing] = await db
    .select({ id: users.id })
    .from(users)
    .where(and(eq(users.email, email), ne(users.id, userId)))
    .limit(1)

  if (existing) {
    return { ok: false as const, error: 'email_taken' as const }
  }

  const emailChanged = current.email.toLowerCase() !== email

  const [updated] = await db
    .update(users)
    .set({
      name,
      email,
      emailVerified: emailChanged ? false : current.emailVerified,
      updatedAt: new Date(),
    })
    .where(eq(users.id, userId))
    .returning()

  return {
    ok: true as const,
    user: updated,
    emailChanged,
  }
}
