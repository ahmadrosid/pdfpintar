import { describe, expect, it } from 'vitest'

import { users } from '#/db/schema'
import { createTestDb } from '#/db/test-utils'
import {
  updateUserProfile,
  validatePasswordChange,
  validateProfileInput,
} from '#/lib/auth/profile'

describe('profile auth helpers', () => {
  it('validates profile input', () => {
    expect(validateProfileInput({ name: '', email: 'a@b.com' })).toBe(
      'name_required',
    )
    expect(
      validateProfileInput({ name: 'Ada', email: 'not-an-email' }),
    ).toBe('invalid_email')
    expect(validateProfileInput({ name: 'Ada', email: 'ada@example.com' })).toBe(
      null,
    )
  })

  it('updates name and email for the current user', async () => {
    const { db } = await createTestDb()

    const [user] = await db
      .insert(users)
      .values({
        name: 'Old Name',
        email: 'old@example.com',
        emailVerified: true,
      })
      .returning()

    const result = await updateUserProfile(db, user.id, {
      name: 'New Name',
      email: 'new@example.com',
    })

    expect(result.ok).toBe(true)
    if (!result.ok) return

    expect(result.user.name).toBe('New Name')
    expect(result.user.email).toBe('new@example.com')
    expect(result.user.emailVerified).toBe(false)
  })

  it('keeps email verification when email is unchanged', async () => {
    const { db } = await createTestDb()

    const [user] = await db
      .insert(users)
      .values({
        name: 'Verified User',
        email: 'verified@example.com',
        emailVerified: true,
      })
      .returning()

    const result = await updateUserProfile(db, user.id, {
      name: 'Verified User Updated',
      email: 'verified@example.com',
    })

    expect(result.ok).toBe(true)
    if (!result.ok) return

    expect(result.user.emailVerified).toBe(true)
  })

  it('rejects duplicate email addresses', async () => {
    const { db } = await createTestDb()

    const [owner] = await db
      .insert(users)
      .values({ name: 'Owner', email: 'owner@example.com' })
      .returning()
    await db
      .insert(users)
      .values({ name: 'Other', email: 'taken@example.com' })

    const result = await updateUserProfile(db, owner.id, {
      name: 'Owner',
      email: 'taken@example.com',
    })

    expect(result).toEqual({ ok: false, error: 'email_taken' })
  })

  it('validates password change input', () => {
    expect(
      validatePasswordChange({
        currentPassword: '',
        password: 'newpassword',
        passwordConfirmation: 'newpassword',
      }),
    ).toBe('current_password_required')

    expect(
      validatePasswordChange({
        currentPassword: 'oldpassword',
        password: 'short',
        passwordConfirmation: 'short',
      }),
    ).toBe('password_too_short')

    expect(
      validatePasswordChange({
        currentPassword: 'oldpassword',
        password: 'newpassword',
        passwordConfirmation: 'different',
      }),
    ).toBe('password_mismatch')
  })
})
