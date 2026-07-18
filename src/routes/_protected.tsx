import { Outlet, createFileRoute, redirect } from '@tanstack/react-router'

import { getSession } from '#/lib/auth/auth-functions'

export const Route = createFileRoute('/_protected')({
  beforeLoad: async ({ location }) => {
    const session = await getSession()

    if (!session) {
      throw redirect({
        to: '/login',
        search: { redirect: location.href },
      })
    }

    return { session }
  },
  component: ProtectedLayout,
})

function ProtectedLayout() {
  return (
    <main className="page-wrap px-4 py-8">
      <Outlet />
    </main>
  )
}
