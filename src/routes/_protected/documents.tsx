import { Outlet, createFileRoute } from '@tanstack/react-router'

export const Route = createFileRoute('/_protected/documents')({
  component: DocumentsLayout,
})

function DocumentsLayout() {
  return (
    <div className="-mx-4 -my-8 min-h-[calc(100vh-5rem)]">
      <Outlet />
    </div>
  )
}
