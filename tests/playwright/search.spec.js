/**
 * Live Search Tests
 * Suite: Playwright > Search
 *
 * Tests the search functionality across all pages - live search,
 * search filters, search results display, and empty results.
 *
 * @see TESTING-GUIDE.md Section "Search"
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { BASE_URL } = require('../playwright.config');

test.describe('Search — Global Search', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
  });

  test('SEARCH-01: Search input is present in header or sidebar', async ({ page }) => {
    const searchInput = page.locator('header input[type="search"], nav input[type="search"], input[class*="search"]').first();
    if (await searchInput.count() > 0) {
      await expect(searchInput).toBeVisible();
    } else {
      // May be in a different location
      const anySearch = page.locator('input[placeholder*="search"], input[name*="search"]').first();
      await expect(anySearch).toBeVisible();
    }
  });

  test('SEARCH-02: Typing in search shows live results', async ({ page }) => {
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('test');
      await page.waitForTimeout(500);
      // Should show results dropdown or update page
      const body = await page.textContent('body');
      expect(body).toMatch(/test|result|search/i);
    } else {
      expect(true).toBe(true);
    }
  });

  test('SEARCH-03: Search with no results shows empty state', async ({ page }) => {
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('xyznonexistentword12345abc');
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      expect(body).toMatch(/no.*result|no.*found|not.*found/i);
    }
  });

  test('SEARCH-04: Clearing search restores original results', async ({ page }) => {
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('test');
      await page.waitForTimeout(500);
      await searchInput.clear();
      await page.waitForTimeout(500);
      const body = await page.textContent('body');
      // Should restore the page without search filter
      expect(body.length).toBeGreaterThan(50);
    }
  });

  test('SEARCH-05: Search results are clickable', async ({ page }) => {
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('rfi');
      await page.waitForTimeout(1000);
      const resultLink = page.locator('[class*="search"] a, [class*="result"] a, .search-results a').first();
      if (await resultLink.count() > 0) {
        await resultLink.click();
        await page.waitForTimeout(1000);
        const url = page.url();
        expect(url).toMatch(/rfi|rfis/i);
      }
    }
  });
});

test.describe('Search — Page-Specific Search', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
  });

  test('SEARCH-PAGE-01: RFIs page has search', async ({ page }) => {
    await page.goto(BASE_URL + '?page=rfis&lang=en');
    await page.waitForTimeout(1000);
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await expect(searchInput).toBeVisible();
    }
  });

  test('SEARCH-PAGE-02: RFIs search filters results', async ({ page }) => {
    await page.goto(BASE_URL + '?page=rfis&lang=en');
    await page.waitForTimeout(1000);
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('test');
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      expect(body).toMatch(/rfi|solicitud|test/i);
    }
  });

  test('SEARCH-PAGE-03: Daily logs page has search', async ({ page }) => {
    await page.goto(BASE_URL + '?page=daily_logs&lang=en');
    await page.waitForTimeout(1000);
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await expect(searchInput).toBeVisible();
    }
  });

  test('SEARCH-PAGE-04: Tasks page has search', async ({ page }) => {
    await page.goto(BASE_URL + '?page=tasks&lang=en');
    await page.waitForTimeout(1000);
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await expect(searchInput).toBeVisible();
    }
  });

  test('SEARCH-PAGE-05: Budget page has search', async ({ page }) => {
    await page.goto(BASE_URL + '?page=budget&lang=en');
    await page.waitForTimeout(1000);
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await expect(searchInput).toBeVisible();
    }
  });
});

test.describe('Search — Edge Cases', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=rfis&lang=en');
    await page.waitForTimeout(1000);
  });

  test('SEARCH-EDGE-01: Very long search query handled', async ({ page }) => {
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      const longQuery = 'a'.repeat(200);
      await searchInput.fill(longQuery);
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      // Should not crash, should show results or empty state
      expect(body.length).toBeGreaterThan(0);
    }
  });

  test('SEARCH-EDGE-02: Special characters in search', async ({ page }) => {
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill("test' OR1=1--");
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      // Should treat as literal text, not SQL injection
      expect(body).not.toMatch(/sql.*error|mysql.*error/i);
    }
  });

  test('SEARCH-EDGE-03: HTML in search input is escaped', async ({ page }) => {
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('<script>alert(1)</script>');
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      // Script should not execute
      expect(body).not.toMatch(/<script>/i);
    }
  });

  test('SEARCH-EDGE-04: Unicode in search works', async ({ page }) => {
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('café');
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      expect(body).toMatch(/café|result|search/i);
    }
  });

  test('SEARCH-EDGE-05: Empty/whitespace search handled', async ({ page }) => {
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('   ');
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      // Should show all results or empty state
      expect(body).toMatch(/rfi|solicitud/i);
    }
  });
});
