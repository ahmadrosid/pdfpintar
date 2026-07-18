# PDFPintar → TanStack Start Migration Plan

**Status:** Draft  
**Date:** 2026-07-18  
**Goal:** Full rewrite from Laravel + Livewire + Svelte to TanStack Start (React, server functions, SSR).  
**Decision:** Replace the Laravel application surface area with a single TanStack Start app. Keep the existing database schema and S3/OpenAI integrations where possible.

---

## 1. Problem Frame

PDFPintar is a PDF chat application. Today it runs on a **hybrid stack**:

| Layer | Current | Target |
|-------|---------|--------|
| Server framework | Laravel 11 | TanStack Start |
| Interactive UI | Livewire 3 + Volt + custom Svelte bridge | React 19 + TanStack Router |
| Data fetching | Livewire wire calls, raw `fetch` | TanStack Query |
| PDF viewer | React island (`react-app.jsx`) | React route component |
| Auth | Breeze + Livewire Volt + Firebase Google login | Better Auth (or Lucia) + Google OAuth |
| ORM | Eloquent | Drizzle |
| Admin | Filament 3 | Deferred — minimal protected admin routes |
| Queues / ops | Horizon, Pulse, Spatie backup | Defer — not on critical user path |

The custom `App\Support\Svelte` bridge and `mingle.svelte.js` lifecycle hooks are the main complexity drivers. A full TanStack rewrite removes this glue and consolidates on one UI runtime (React).

---

## 2. Scope

### In scope (MVP parity)

- Landing / welcome page
- Auth: register, login, logout, forgot/reset password, email verification (configurable), Google sign-in
- Dashboard: document list, search, upload, delete
- Document chat: split PDF viewer + streaming chat (SSE)
- Public share links (`/share/{token}`)
- Copy shared document to own library
- Toggle document public/private + copy share link
- i18n (EN + ID from `resources/lang/id.json`)
- Markdown → PDF tool (`/pdf-generator` equivalent)
- Core API: documents, threads, messages, chat stream

### Out of scope (phase 2+)

- Filament admin panel parity (users, documents, threads CRUD)
- Horizon job dashboard
- Laravel Pulse metrics
- Spatie backup / restore artisan commands
- Support bubble widget
- Chat message download as Excel/Word (currently commented out in Svelte)

### Keep from Laravel repo (reference only during migration)

Use existing code as the behavioral spec:

- `app/Http/Controllers/ChatController.php` — OpenAI Assistants streaming
- `app/Http/Controllers/DocumentController.php` — upload + copy
- `app/Livewire/*.php` — UI state and authorization rules
- `resources/js/components/*.svelte` — component behavior
- `database/migrations/*` — schema source of truth

---

## 3. Target Architecture

```
pdfpintar/
├── app/                          # TanStack Start app (new)
│   ├── routes/                   # File-based routes
│   │   ├── __root.tsx
│   │   ├── index.tsx             # Welcome
│   │   ├── dashboard.tsx
│   │   ├── documents/
│   │   │   ├── index.tsx
│   │   │   └── $documentId.tsx   # Chat + PDF viewer
│   │   ├── share/
│   │   │   └── $token.tsx        # Public read-only chat
│   │   ├── profile.tsx
│   │   ├── pdf-generator.tsx
│   │   └── api/                  # Server routes (SSE, uploads)
│   │       └── chat/
│   │           └── stream.ts
│   ├── components/               # React UI (ported from Svelte)
│   ├── lib/
│   │   ├── db/                   # Drizzle schema + client
│   │   ├── auth/                 # Better Auth config
│   │   ├── openai/               # Assistants + file upload
│   │   ├── storage/              # S3 presigned URLs
│   │   └── processors/           # PDF/Excel/Word export
│   └── server/                   # Server functions
│       ├── documents.ts
│       ├── chat.ts
│       └── auth.ts
├── drizzle/                      # Migrations (ported from Laravel)
├── public/
├── package.json
└── legacy/                       # Optional: archived Laravel app during transition
```

**Runtime:** Node 22+, deployable to Vercel/Netlify/Cloudflare or Bun/Docker.

**Database:** Reuse existing MySQL/SQLite schema via Drizzle introspection or hand-written schema matching `database/migrations/`.

---

## 4. Feature Inventory → Route Map

| Current route / component | New TanStack route | Server work |
|---------------------------|-------------------|-------------|
| `GET /` welcome blade | `routes/index.tsx` | Static SSR |
| Volt auth pages | `routes/_auth/login.tsx`, `register.tsx`, etc. | Better Auth |
| `GET /dashboard` | `routes/dashboard.tsx` | Loader: user documents |
| `GET /documents/{id}` | `routes/documents/$documentId.tsx` | Loader: document, thread, messages, presigned PDF URL |
| `POST /chat/stream` | `routes/api/chat/stream.ts` (server route) | OpenAI streamed SSE |
| `POST /upload-documents` | `createServerFn` `uploadDocument` | S3 + OpenAI file upload |
| `GET /share/{token}` | `routes/share/$token.tsx` | Public document + threads |
| `POST /documents/{id}/copy` | `createServerFn` `copyDocument` | S3 copy + DB insert |
| `POST /documents/{id}/share` | `createServerFn` `toggleShare` | Token + is_public |
| `GET /profile` | `routes/profile.tsx` | Auth user update |
| `GET /pdf-generator` | `routes/pdf-generator.tsx` | Markdown preview + PDF download |
| `GET /shared-documents` | `routes/shared-documents.tsx` | Paginated shared docs |
| Firebase Google callback | Better Auth Google provider | Verify ID token server-side |
| Filament `/admin` | **Deferred** | — |

---

## 5. Migration Phases

### Phase 0 — Bootstrap (1–2 days)

**Goal:** Runnable TanStack Start shell with DB connection and auth stub.

1. Scaffold with `npm create @tanstack/start@latest` in repo root (or `app/` subfolder — pick one layout and stick to it).
2. Add dependencies:
   - `@tanstack/react-router`, `@tanstack/react-query`, `@tanstack/react-start`
   - `drizzle-orm`, `drizzle-kit`, driver (`better-sqlite3` or `mysql2`)
   - `better-auth` (+ Google provider)
   - `@aws-sdk/client-s3`, `@aws-sdk/s3-request-presigner`
   - `openai`
   - `@react-pdf-viewer/core`, `@react-pdf-viewer/default-layout`, `pdfjs-dist`
   - `react-markdown`, `shiki` (replace `svelte-exmarkdown`)
   - `tailwindcss`, `lucide-react`, shadcn/ui (replace bits-ui Svelte components)
3. Port Drizzle schema from Laravel migrations:
   - `users` (+ `google_id`, `email_verified_at`)
   - `documents` (+ `file_path`, `sharing_token`, `is_public`)
   - `threads` (+ `title`)
   - `messages`
4. Environment variables (from `.env.example`):
   - `DATABASE_URL`, `OPENAI_API_KEY`, `OPENAI_ORGANIZATION`
   - `AWS_*`, `BETTER_AUTH_SECRET`, `GOOGLE_CLIENT_ID/SECRET` or Firebase credentials
   - `REQUIRE_EMAIL_VERIFICATION`, `SUPER_ADMIN_EMAIL`
5. Set up Tailwind + dark mode to match current `resources/css/app.css` tokens.

**Exit criteria:** `npm run dev` serves `/`, DB connects, health check server function works.

---

### Phase 1 — Auth (2–3 days)

**Goal:** Replace Breeze + Volt + Firebase callback.

| Laravel behavior | TanStack implementation |
|------------------|-------------------------|
| Session auth (Breeze) | Better Auth session cookies |
| Google login via Firebase token | Better Auth Google OAuth **or** custom credential provider verifying Firebase ID token (`app/Http/Controllers/FirebaseController.php`) |
| Email verification gate on `/documents/{id}` | Route `beforeLoad` guard checking `user.emailVerified` when `REQUIRE_EMAIL_VERIFICATION=true` |
| Profile update / password / delete | Server functions mirroring Volt forms in `resources/views/livewire/profile/` |

**Files to port logic from:**
- `routes/auth.php`
- `resources/views/livewire/pages/auth/*.blade.php`
- `app/Livewire/Forms/LoginForm.php`

**Exit criteria:** Register, login, logout, Google sign-in, protected routes redirect to login.

---

### Phase 2 — Documents CRUD (3–4 days)

**Goal:** Dashboard document list + upload + delete.

Port `DocumentList.svelte` + `DocumentUpload.svelte` → React components.

| Concern | Implementation |
|---------|----------------|
| List + search | TanStack Query `useDocuments({ search })` + server function |
| Upload | `uploadDocument` server function — port `DocumentController::upload` (OpenAI file upload → S3 → DB) |
| Delete | `deleteDocument` with ownership check (mirror `DocumentList::deleteDocument`) |
| Authorization | Policy logic from `app/Policies/DocumentPolicy.php` inlined in server functions |

**Exit criteria:** User can upload PDF, see list, search, delete. Matches current dashboard behavior.

---

### Phase 3 — Chat + PDF Viewer (4–5 days)

**Goal:** Core product — split view chat with streaming.

This is the highest-risk phase.

#### 3a. PDF viewer

Merge `resources/js/react-app.jsx` into `routes/documents/$documentId.tsx`:

- Presigned S3 URL from server loader (replace `Storage::temporaryUrl`)
- Keep `@react-pdf-viewer` + `jump-to-page-plugin.jsx`
- Remove global `createRoot` island pattern

#### 3b. Chat UI

Port `ChatPage.svelte`, `ChatInput.svelte`, `MessageItem.svelte`, `Markdown.svelte`, `CodeBlock.svelte`:

- `react-markdown` + `shiki` for code blocks
- TanStack Query for thread/messages initial load
- Streaming via `fetch` + `ReadableStream` (replace `fetch-event-stream` — use native or `@microsoft/fetch-event-source`)

#### 3c. Chat stream server route

Port `ChatController::stream` to a TanStack server route:

- Validate session + document ownership
- Create OpenAI thread/assistant on first message (mirror `createAssistant`)
- Stream tokens as SSE events matching `ChatEvent` shape: `{ type: 'token' | 'thread' | 'notice', data }`
- Persist messages after stream completes
- Call `TitleGenerator` equivalent (port `app/Support/TitleGenerator.php`)

**Exit criteria:** Full chat flow works with streaming, clear messages, thread title updates.

---

### Phase 4 — Sharing & Public Access (2 days)

**Goal:** Public share links and copy-to-library.

Port:
- `routes/web.php` share route logic
- `PublicChatInterface` (read-only thread selector)
- Share toggle + copy link from `documents/show.blade.php`
- `DocumentController::copy`

**Exit criteria:** Public URL works without auth; authenticated user can copy shared doc.

---

### Phase 5 — Secondary Features (2–3 days)

| Feature | Source | Notes |
|---------|--------|-------|
| Markdown → PDF | `MarkdownConverter` + `PdfProcessor` | Use `@react-pdf/renderer` or `puppeteer` instead of DomPDF |
| Shared documents page | `SharedDocuments` Livewire | TanStack Table optional |
| Locale switcher | `routes/web.php` `/locale/{locale}` | Cookie-based i18n |
| Welcome page | `resources/views/welcome.blade.php` | Static marketing page |

---

### Phase 6 — Cutover & Decommission (1–2 days)

1. Data migration script if schema drifted (shouldn't if Drizzle matches Laravel migrations).
2. Point DNS to new deployment.
3. Archive Laravel code to `legacy/` or separate branch `archive/laravel`.
4. Update `README.md` with new install instructions.
5. Remove: `composer.json`, Livewire views, Svelte bridge, `mingle.svelte.js`, Volt routes.

---

## 6. Key Technical Decisions

### D1: Auth library — Better Auth (recommended)

**Rationale:** First-class TanStack Start integration, session cookies, Google OAuth, email flows. Replaces Breeze + Firebase callback without Sanctum cookie bridging.

**Alternative:** Lucia — more manual but lighter.

### D2: ORM — Drizzle

**Rationale:** Type-safe, works well with TanStack Start server functions, easy SQLite/MySQL switch.

**Migration:** Introspect existing DB or hand-port from `database/migrations/`.

### D3: Streaming — TanStack server route (not server function)

**Rationale:** `ChatController::stream` returns `text/event-stream`. Use a dedicated server route with streaming response; server functions are a poor fit for long-lived SSE.

### D4: File uploads — Server function with FormData

**Rationale:** Multipart upload in `uploadDocument` server function. OpenAI upload + S3 put in sequence (mirror current `DocumentController::upload`).

### D5: PDF presigned URLs — S3 presigner in route loader

**Rationale:** 5-minute TTL matching `routes/web.php`. Never expose raw S3 paths to client.

### D6: UI kit — shadcn/ui (Radix)

**Rationale:** Direct port path from bits-ui Svelte components already in `resources/js/lib/components/ui/`. Lucide icons: `lucide-react`.

### D7: Admin — Defer

**Rationale:** Filament is ~15% of user-facing value. Ship MVP without admin; add minimal `/admin` route later with `SUPER_ADMIN_EMAIL` gate if needed.

### D8: Repo layout — Replace root (recommended)

**Rationale:** Full rewrite.selected. Scaffold TanStack Start at repo root; move Laravel to `legacy/laravel/` on a branch to keep git history clean.

---

## 7. PHP → Node Port Map

| PHP | Node replacement |
|-----|------------------|
| `OpenAI\Laravel\Facades\OpenAI` | `openai` npm package |
| `Storage::` (S3) | `@aws-sdk/client-s3` |
| `Barryvdh\DomPDF` | `@react-pdf/renderer` or `puppeteer` |
| `Maatwebsite\Excel` | `exceljs` |
| `PhpOffice\PhpWord` | `docx` npm package |
| `Str::markdown()` | `marked` or `react-markdown` server render |
| `Kreait\Firebase` | `firebase-admin` for ID token verify |
| Eloquent cascades (`Document::boot`) | Drizzle transaction + manual deletes |
| `ChatEvent::emit()` | SSE `ReadableStream` encoder |
| Laravel policies | Auth checks in server functions |
| `__()` translations | `i18next` or simple JSON loader from `resources/lang/id.json` |

---

## 8. Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| OpenAI Assistants API streaming differences in Node | Chat broken | Port `ChatController` line-by-line; add integration test with mocked stream |
| SSE + SSR interaction | Broken streams behind proxies | Set `Cache-Control: no-cache`, `X-Accel-Buffering: no`; test on target host |
| Firebase → Better Auth Google migration | Existing users can't login | Support both during transition; match on `google_id` / email |
| PDF.js worker CDN | Offline dev breaks | Bundle worker from `pdfjs-dist` like production should |
| Full rewrite timeline | Long feature freeze | Ship phase-by-phase behind feature flag or subdomain `beta.pdfpintar.com` |
| No admin panel | Ops pain | Keep read-only DB access; add minimal admin in Phase 7 |
| AGPL license | Still applies | Preserve `LICENSE`; document stack change |

---

## 9. Test Plan

### Unit / integration (Vitest)

- `lib/openai/chat-stream.test.ts` — mock OpenAI stream, assert SSE event shapes
- `lib/db/documents.test.ts` — CRUD + cascade delete
- `lib/auth/policies.test.ts` — ownership checks
- `lib/storage/presign.test.ts` — URL generation

### E2E (Playwright)

| Scenario | Steps |
|----------|-------|
| Auth flow | Register → verify (if enabled) → login → logout |
| Upload + chat | Login → upload PDF → open document → send message → see streamed reply |
| Public share | Toggle share → open `/share/{token}` in incognito → view messages |
| Copy shared doc | Login as user B → copy from public link → appears in dashboard |
| Delete document | Upload → delete → confirm gone from list |
| Google login | Click Google → redirect → land on dashboard |

### Manual smoke

- Dark mode PDF viewer
- Mobile responsive split view
- Indonesian locale strings
- Large PDF upload (10MB limit)

---

## 10. Implementation Units (ordered)

| # | Unit | Depends on | Est. |
|---|------|------------|------|
| 1 | TanStack Start scaffold + Tailwind + shadcn | — | 1d |
| 2 | Drizzle schema + DB client | 1 | 1d |
| 3 | Better Auth setup | 1, 2 | 2d |
| 4 | Layout + navigation | 3 | 1d |
| 5 | Documents list/upload/delete | 3, 4 | 3d |
| 6 | Document page loader + presigned URL | 5 | 1d |
| 7 | PDF viewer component | 6 | 1d |
| 8 | Chat UI components | 6 | 2d |
| 9 | Chat SSE server route | 8 | 2d |
| 10 | Share + public routes | 9 | 2d |
| 11 | Profile + settings | 3 | 1d |
| 12 | Markdown converter | 4 | 1d |
| 13 | i18n | 4 | 1d |
| 14 | E2E tests + cutover | all | 2d |

**Total estimate:** ~3–4 weeks focused work.

---

## 11. Open Questions (resolve before Phase 3)

1. **Google auth:** Migrate to Better Auth native Google OAuth, or keep Firebase client SDK + server token verification?
2. **Deployment target:** Vercel, Cloudflare Workers, Docker/VPS? (Affects streaming and file upload limits.)
3. **Database:** Keep SQLite for dev and MySQL prod, or standardize on Postgres?
4. **Beta strategy:** Big-bang cutover or run Laravel + TanStack in parallel during migration?

---

## 12. References

- Current Svelte bridge: `app/Support/Svelte.php`, `resources/js/mingle.svelte.js`
- Chat streaming: `app/Http/Controllers/ChatController.php`, `app/Http/ChatEvent.php`
- Core Svelte UI: `resources/js/components/ChatPage.svelte`, `DocumentList.svelte`
- PDF viewer: `resources/js/react-app.jsx`
- Schema: `database/migrations/`
- [TanStack Start docs](https://tanstack.com/start/latest)
- [Better Auth TanStack integration](https://www.better-auth.com/docs/integrations/tanstack)
