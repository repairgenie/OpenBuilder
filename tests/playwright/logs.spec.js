const { test, expect } = require('@playwright/test');

test('Daily Log UI Verification', async ({ page }) => {
  await page.goto('http://localhost:8000?page=daily_logs&lang=en');
  
  // Verify Cards exist (if data is present)
  const cards = page.locator('.card');
  if (await cards.count() > 0) {
    await expect(cards.first()).toBeVisible();
    await expect(cards.first().locator('h3')).toContainText('Project Report');
  }
  
  // Switch to Spanish
  await page.goto('http://localhost:8000?page=daily_logs&lang=es');
  if (await cards.count() > 0) {
    await expect(cards.first().locator('h3')).toContainText('Informe de Obra');
  }
});
