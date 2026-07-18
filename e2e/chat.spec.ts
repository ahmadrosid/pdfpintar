import { expect, test } from '@playwright/test'

test.describe('pdf generator', () => {
  test('shows validation when markdown is empty', async ({ page }) => {
    await page.goto('/pdf-generator')
    await expect(
      page.getByRole('heading', { name: 'Markdown to PDF Converter' }),
    ).toBeVisible()
    await page.getByRole('button', { name: 'Convert to PDF' }).click()
    await expect(page.getByText('Markdown content is required.')).toBeVisible()
  })

  test('accepts markdown input and shows preview', async ({ page }) => {
    await page.goto('/pdf-generator')
    await page.getByPlaceholder('Enter your Markdown text here...').fill(
      '# Hello PDFPintar',
    )
    await expect(
      page.getByRole('heading', { name: 'Hello PDFPintar' }),
    ).toBeVisible()
  })
})

test.describe('protected chat routes', () => {
  test('document route requires authentication', async ({ page }) => {
    await page.goto('/documents/1')
    await expect(page).toHaveURL(/\/login/)
  })
})
