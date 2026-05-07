const { test, expect } = require('@playwright/test');

test('Live Search Test', async ({ page }) => {
  await page.goto('http://localhost:8000');

  // Click on the Search button in the sidebar (we have to find it carefully)
  // Let's just navigate using the URL or trigger the global search function if it exists.
  // Oh, wait, "Search" in mobile nav is a button with onclick: window.modals['search-modal'].open()
  await page.evaluate(() => window.modals['search-modal'].open());

  const searchInput = page.locator('#modal-search-input');
  await expect(searchInput).toBeVisible();
  
  await searchInput.fill('Design');
  
  // Wait for results to appear
  const results = page.locator('#modal-search-results');
  await expect(results).toBeVisible();
  await expect(results).toContainText('Design System');
});
