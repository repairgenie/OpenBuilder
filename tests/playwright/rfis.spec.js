const { test, expect } = require('@playwright/test');

test('RFI UI Verification', async ({ page }) => {
  await page.goto('http://localhost:8000?page=rfis&lang=en');
  
  // Verify Table Headers
  await expect(page.locator('th').first()).toBeVisible();
  
  // Switch to Spanish
  await page.goto('http://localhost:8000?page=rfis&lang=es');
  const headersEs = await page.locator('th').allTextContents();
  expect(headersEs.map(h => h.trim())).toContain('Referencia');
});
