const { test, expect } = require('@playwright/test');

test('Basic Navigation Test', async ({ page }) => {
  // Access the local server (assumed to be running on 8000)
  await page.goto('http://localhost:8000');
  
  // Check title
  await expect(page).toHaveTitle(/OpenBuilder/);
});
