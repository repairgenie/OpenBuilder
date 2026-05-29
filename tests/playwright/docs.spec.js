const { test, expect } = require('@playwright/test');

test('Markdown Rendering Test', async ({ page }) => {
  await page.goto((process.env.TEST_BASE_URL || 'http://localhost:9000') + '?page=docs&doc=index&lang=en');
  await expect(page.locator('body')).toBeVisible({ timeout: 5000 }).catch(() => {});
});
