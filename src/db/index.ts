import { drizzle } from 'drizzle-orm/node-postgres'
import type { NodePgDatabase } from 'drizzle-orm/node-postgres'
import pg from 'pg'

import * as schema from './schema.ts'

const { Pool } = pg

type Db = NodePgDatabase<typeof schema>

let pool: pg.Pool | undefined
let dbInstance: Db | undefined

function createPool() {
  const url = process.env.DATABASE_URL
  if (!url) {
    throw new Error('DATABASE_URL is not set')
  }
  return new Pool({ connectionString: url })
}

export function getPool() {
  if (!pool) {
    pool = createPool()
  }
  return pool
}

export function getDb(): Db {
  if (!dbInstance) {
    dbInstance = drizzle(getPool(), { schema })
  }
  return dbInstance
}

/** @deprecated Prefer getDb() — kept for drizzle adapter wiring */
export const db = new Proxy({} as Db, {
  get(_target, prop, receiver) {
    return Reflect.get(getDb() as object, prop, receiver)
  },
})
