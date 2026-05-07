const { test, expect } = require('@playwright/test');

test('Navigation Router Test', async ({ page }) => {
  await page.goto('http://localhost:8000');

  // Test Dashboard (default)
  await expect(page.locator('h2').first()).toContainText('Dashboard');

  // Test RFI page
  await page.goto('http://localhost:8000?page=rfis&lang=en');
  await expect(page).toHaveURL(/page=rfis/);
  await expect(page.locator('h2').first()).toContainText('Requests for Information');

  // Test Daily Logs page
  await page.goto('http://localhost:8000?page=daily_logs&lang=en');
  await expect(page).toHaveURL(/page=daily_logs/);
  await expect(page.locator('h2').first()).toContainText('Daily Logs');
});
