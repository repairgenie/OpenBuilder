const { test, expect } = require('@playwright/test');

test('Dynamic Navigation and Language Test', async ({ page }) => {
  await page.goto('http://localhost:8000');
  
  // Switch to Spanish via explicitly clicking the anchor with ?lang=es
  await page.click('a[href="?page=dashboard&lang=es"]');
  await expect(page).toHaveURL(/lang=es/);
  await expect(page.locator('h2').first()).toContainText('Panel de Control');
});
