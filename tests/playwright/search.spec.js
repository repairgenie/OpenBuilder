const { test, expect } = require('@playwright/test');

test('Live Search Test', async ({ page }) => {
  await page.goto('http://localhost:8000?lang=en');
  
  const searchInput = page.locator('input[name="search"]');
  await searchInput.fill('Design');
  
  // Wait for results to appear
  const results = page.locator('#search-results');
  await expect(results).toBeVisible();
  await expect(results).toContainText('Design System');
  
  // Test no results
  await searchInput.fill('NonExistentPage');
  await expect(results).toContainText('No results found');
});
