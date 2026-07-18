import { expect, test } from '@playwright/test'

test.describe('public sharing', () => {
  test('invalid share token returns not found', async ({ page }) => {
    const response = await page.goto('/share/not-a-real-token')
    expect(response?.status()).toBe(404)
  })

  test('shared documents page requires authentication', async ({ page }) => {
    await page.goto('/shared-documents')
    await expect(page).toHaveURL(/\/login/)
  })
})
