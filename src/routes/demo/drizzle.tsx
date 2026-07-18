import { createFileRoute } from '@tanstack/react-router'

export const Route = createFileRoute('/demo/drizzle')({
  component: DrizzleInfoPage,
})

function DrizzleInfoPage() {
  return (
    <main className="page-wrap px-4 py-12">
      <section className="island-shell rounded-2xl p-6 sm:p-8">
        <p className="island-kicker mb-2">Database</p>
        <h1 className="display-title mb-3 text-3xl font-bold text-[var(--sea-ink)]">
          Drizzle schema
        </h1>
        <p className="m-0 max-w-2xl text-[var(--sea-ink-soft)]">
          PDFPintar uses Drizzle with PostgreSQL. App tables: users, documents,
          threads, messages. Auth tables: sessions, accounts, verifications.
          Run <code>npm run db:push</code> after setting <code>DATABASE_URL</code>.
        </p>
      </section>
    </main>
  )
}
