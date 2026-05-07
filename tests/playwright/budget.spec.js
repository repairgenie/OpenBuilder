const { test, expect } = require('@playwright/test');

test('Budget UI Verification', async ({ page }) => {
  await page.goto('http://localhost:8000?page=budget&lang=en');
  
  // Verify Progress Bar
  await expect(page.locator('.bg-primary.h-2.5')).toBeVisible();
  
  // Verify Variance Class
  const varianceCell = page.locator('.text-success, .text-danger, .text-warning');
  await expect(varianceCell.first()).toBeVisible();
  
  // Switch to Spanish
  await page.goto('http://localhost:8000?page=budget&lang=es');
  await expect(page.locator('h2')).toContainText('Presupuesto');
});
