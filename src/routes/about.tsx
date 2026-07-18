import { createFileRoute } from '@tanstack/react-router'

export const Route = createFileRoute('/about')({
  component: About,
})

function About() {
  return (
    <main className="page-wrap px-4 py-12">
      <section className="max-w-3xl">
        <p className="mb-2 text-sm font-medium text-neutral-500 dark:text-neutral-400">
          About
        </p>
        <h1 className="mb-3 text-4xl font-bold tracking-tight text-neutral-900 sm:text-5xl dark:text-neutral-50">
          Chat with your PDFs.
        </h1>
        <p className="m-0 max-w-2xl text-base leading-7 text-neutral-500 dark:text-neutral-400">
          PDFPINTAR turns documents into conversations. Upload a PDF, ask
          questions, and get answers grounded in your file.
        </p>
      </section>
    </main>
  )
}
