import { createStore, type Plugin } from '@react-pdf-viewer/core'

export default function jumpToPagePlugin(): Plugin & {
  jumpToPage: (pageIndex: number) => void
} {
  const store = createStore<Record<string, unknown>>()

  return {
    install: (pluginFunctions) => {
      store.update('jumpToPage', pluginFunctions.jumpToPage)
    },
    jumpToPage: (pageIndex: number) => {
      const jump = store.get('jumpToPage') as
        | ((page: number) => void)
        | undefined
      jump?.(pageIndex)
    },
  }
}
