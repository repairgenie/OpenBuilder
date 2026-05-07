const { test, expect } = require('@playwright/test');

test('Modular Layout Verification', async ({ page }) => {
  await page.goto('http://localhost:8000');
  
  // Verify Header/Sidebar (from previous test)
  await expect(page.locator('aside')).toBeVisible();
  
  // Verify Topbar
  await expect(page.locator('header.sticky')).toBeVisible();
  await expect(page.locator('input[name="search"]')).toBeVisible();
  
  // Verify Footer
  await expect(page.locator('footer')).toBeVisible();
  await expect(page.locator('footer')).toContainText('OpenBuilder');
});
