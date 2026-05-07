const { test, expect } = require('@playwright/test');

test('Navigation Router Test', async ({ page }) => {
  await page.goto('http://localhost:8000');
  await expect(page).toHaveTitle(/OpenBuilder/);

  // Test RFI page
  await page.click('text=RFIs / Solicitudes');
  await expect(page).toHaveURL(/page=rfis/);
  await expect(page.locator('h2')).toContainText('Requests for Information');

  // Test Daily Logs page
  await page.click('text=Daily Logs / Diarios');
  await expect(page).toHaveURL(/page=daily_logs/);
  await expect(page.locator('h2')).toContainText('Daily Logs');
});
