const { test, expect } = require('@playwright/test');

test('Markdown Rendering Test', async ({ page }) => {
  await page.goto('http://localhost:8000?page=docs&doc=index&lang=en');
  
  // Verify H1 rendering
  await expect(page.locator('h1')).toContainText('Documentation Index');
  
  // Verify Link rendering
  await expect(page.locator('text=Design System')).toBeVisible();
  
  // Switch to Spanish
  await page.goto('http://localhost:8000?page=docs&doc=index&lang=es');
  await expect(page.locator('h1')).toContainText('Índice de Documentación');
});
