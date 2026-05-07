const { test, expect } = require('@playwright/test');

test('Live Search Test', async ({ page }) => {
  await page.goto('http://localhost:8000');

  await page.waitForLoadState('networkidle');

  // Trigger search via keyboard shortcut
  await page.keyboard.press('Control+k');

  const searchInput = page.locator('#modal-search-input');
  await expect(searchInput).toBeVisible();
  
  await searchInput.fill('Design');
  
  // Wait for results to appear
  const results = page.locator('#modal-search-results');
  await expect(results).toBeVisible();
  await expect(results).toContainText('Design System');
});
