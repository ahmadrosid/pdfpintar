# PDFPintar

Chat with your PDF documents using AI. This repository is the **TanStack Start** application (React 19, SSR, server functions). The original Laravel stack is archived under [`legacy/laravel/`](legacy/laravel/).

## Stack

- [TanStack Start](https://tanstack.com/start) + React 19 + Vite
- PostgreSQL via [Drizzle ORM](https://orm.drizzle.team/)
- [Better Auth](https://www.better-auth.com/) (email/password + Google OAuth)
- OpenAI Assistants API (streaming chat)
- AWS S3 (private PDF storage + presigned URLs)

## Requirements

- **Node.js 22+** (TanStack Start engine requirement)
- PostgreSQL database
- OpenAI API key with Assistants access
- AWS S3 bucket and credentials
- Google OAuth credentials (optional, for social login)

## Quick start

```bash
npm install
cp .env.example .env.local
# Edit .env.local — see Environment variables below
npm run db:push
npm run dev
```

Open http://localhost:3000

## Docker Compose

Run PostgreSQL and MinIO (S3-compatible storage) locally without installing them on your machine.

```bash
cp .env.docker.example .env.local
# Add OPENAI_API_KEY and optionally GOOGLE_* to .env.local
docker compose up -d
npm run db:push
npm run dev
```

| Service | URL | Credentials |
|---------|-----|-------------|
| PostgreSQL | `localhost:5433` | user `pdfpintar`, password `pdfpintar`, db `pdfpintar` |
| MinIO API | http://localhost:9000 | `minioadmin` / `minioadmin` |
| MinIO Console | http://localhost:9001 | same as above |

Useful commands:

| Command | Description |
|---------|-------------|
| `npm run docker:up` | Start Postgres + MinIO in the background |
| `npm run docker:down` | Stop and remove containers |
| `npm run docker:logs` | Follow container logs |

To run the app inside Docker (with hot reload via volume mount):

```bash
docker compose --profile app up
```

Production image build:

```bash
docker build --target production -t pdfpintar .
docker run --rm -p 3000:3000 --env-file .env.local pdfpintar
```

## Environment variables

| Variable | Required | Description |
|----------|----------|-------------|
| `DATABASE_URL` | Yes | PostgreSQL connection string |
| `BETTER_AUTH_SECRET` | Yes | Random secret for session signing |
| `BETTER_AUTH_URL` | Yes | App URL (e.g. `http://localhost:3000`) |
| `OPENAI_API_KEY` | Yes | OpenAI API key |
| `OPENAI_ORGANIZATION` | No | OpenAI organization ID |
| `AWS_ACCESS_KEY_ID` | Yes | S3 access key |
| `AWS_SECRET_ACCESS_KEY` | Yes | S3 secret key |
| `AWS_DEFAULT_REGION` | Yes | S3 region |
| `AWS_BUCKET` | Yes | S3 bucket name |
| `AWS_S3_ENDPOINT` | No | Custom S3 endpoint (e.g. MinIO at `http://localhost:9000`) |
| `AWS_S3_PUBLIC_ENDPOINT` | No | Public URL for presigned links when app runs in Docker |
| `AWS_USE_PATH_STYLE_ENDPOINT` | No | Set `true` for MinIO |
| `GOOGLE_CLIENT_ID` | No | Google OAuth client ID |
| `GOOGLE_CLIENT_SECRET` | No | Google OAuth client secret |
| `REQUIRE_EMAIL_VERIFICATION` | No | Set `true` to require verified email |
| `SUPER_ADMIN_EMAIL` | No | Reserved for future admin tooling |

## Scripts

| Command | Description |
|---------|-------------|
| `npm run dev` | Start development server on port 3000 |
| `npm run build` | Production build |
| `npm run preview` | Preview production build |
| `npm test` | Run Vitest unit/integration tests |
| `npm run test:e2e` | Run Playwright E2E tests |
| `npm run db:push` | Push Drizzle schema to database |
| `npm run db:studio` | Open Drizzle Studio |
| `npm run generate-routes` | Regenerate TanStack Router route tree |

## Features

- **Dashboard** — upload, search, delete PDFs
- **Document chat** — streaming AI replies with inline PDF viewer
- **Sharing** — public read-only links at `/share/{token}`
- **Profile** — update name/email/password, delete account
- **Markdown to PDF** — `/pdf-generator` tool
- **i18n** — English and Indonesian (cookie-based locale)

## Google OAuth migration

Legacy users signed in via Firebase Google login. The TanStack app uses Better Auth Google OAuth. Users are matched on **email** or **google_id**. Configure `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` and add the Better Auth callback URL to your Google Cloud console.

## Legacy Laravel app

The previous Laravel + Livewire + Svelte application lives in [`legacy/laravel/`](legacy/laravel/). See that directory's README to run the old stack for reference during migration.

## Smoke checklist

After setup, verify:

1. Register or log in at `/login`
2. Upload a PDF on `/dashboard`
3. Open the document and send a chat message
4. Toggle sharing and open `/share/{token}` in incognito
5. Update profile at `/profile`
6. Convert markdown at `/pdf-generator`

## Migration plan

Implementation plan: [`docs/plans/2026-07-18-001-refactor-laravel-tanstack-migration-plan.md`](docs/plans/2026-07-18-001-refactor-laravel-tanstack-migration-plan.md)

## License

AGPL-3.0 — see [`LICENSE`](LICENSE).
