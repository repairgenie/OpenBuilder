const { test, expect } = require('@playwright/test');

test('Budget UI Verification', async ({ page }) => {
  await page.goto('http://localhost:8000?page=budget&lang=en');
  
  // Verify Variance Class
  const varianceCell = page.locator('.text-danger').first();
  await expect(varianceCell).toBeVisible();
  
  // Verify slider
  await expect(page.locator('#variance-slider')).toBeVisible();
});
