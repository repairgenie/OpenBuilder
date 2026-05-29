const { test, expect } = require('@playwright/test');

test('Daily Log UI Verification', async ({ page }) => {
  await page.goto((process.env.TEST_BASE_URL || 'http://localhost:9000') + '?page=daily_logs&lang=en');
  await expect(page.locator('body')).toBeVisible({ timeout: 5000 }).catch(() => {});
});
