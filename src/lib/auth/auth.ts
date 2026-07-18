import { betterAuth } from 'better-auth'
import { drizzleAdapter } from '@better-auth/drizzle-adapter'
import { tanstackStartCookies } from 'better-auth/tanstack-start'

import { db } from '#/db/index'
import { authSchema } from '#/db/schema'

const baseURL = process.env.BETTER_AUTH_URL ?? 'http://localhost:3000'

export const auth = betterAuth({
  baseURL,
  secret: process.env.BETTER_AUTH_SECRET ?? 'dev-secret-change-me',
  database: drizzleAdapter(db, {
    provider: 'pg',
    schema: authSchema,
  }),
  advanced: {
    database: {
      // users.id is serial; sessions/accounts/verifications use text UUIDs
      generateId: (options) => {
        if (options.model === 'user' || options.model === 'users') {
          return false
        }
        return crypto.randomUUID()
      },
    },
  },
  user: {
    additionalFields: {
      googleId: {
        type: 'string',
        required: false,
        input: false,
      },
    },
    changeEmail: {
      enabled: true,
      updateEmailWithoutVerification: true,
    },
    deleteUser: {
      enabled: true,
    },
  },
  emailAndPassword: {
    enabled: true,
    requireEmailVerification:
      process.env.REQUIRE_EMAIL_VERIFICATION === 'true',
  },
  socialProviders: {
    google: {
      clientId: process.env.GOOGLE_CLIENT_ID ?? '',
      clientSecret: process.env.GOOGLE_CLIENT_SECRET ?? '',
      enabled: Boolean(
        process.env.GOOGLE_CLIENT_ID && process.env.GOOGLE_CLIENT_SECRET,
      ),
    },
  },
  plugins: [tanstackStartCookies()],
})

export type Session = typeof auth.$Infer.Session
