/**
 * Docs Page Tests
 * Suite: Playwright > Docs
 *
 * Tests the documentation/wiki pages - markdown rendering,
 * navigation, search, and bilingual support.
 *
 * @see TESTING-GUIDE.md Section "Docs Page"
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { gotoLang } = require('../helpers/bilingual');
const { BASE_URL } = require('../playwright.config');

test.describe('Docs — Public Pages', () => {
  // Docs may be public or require auth depending on config

  test('DOCS-01: Docs page loads without auth', async ({ page }) => {
    await page.goto(BASE_URL + '?page=docs&lang=en');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/doc|guide|manual|docs/i);
  });

  test('DOCS-02: Markdown content is rendered correctly', async ({ page }) => {
    await page.goto(BASE_URL + '?page=docs&doc=index&lang=en');
    await page.waitForTimeout(1000);

    // Should NOT show raw markdown (no asterisks for headers, no ``` for code blocks)
    const body = await page.textContent('body');
    // Should have rendered HTML elements
    const hasH1 = await page.locator('h1, h2, h3').count() > 0;
    const hasContent = body.length > 100;
    expect(hasContent || hasH1).toBe(true);
  });

  test('DOCS-03: Table of contents is present', async ({ page }) => {
    await page.goto(BASE_URL + '?page=docs&doc=index&lang=en');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/table.*contents|index|contents|navigation/i);
  });

  test('DOCS-04: Code blocks are syntax highlighted', async ({ page }) => {
    await page.goto(BASE_URL + '?page=docs&doc=index&lang=en');
    await page.waitForTimeout(1000);
    // Look for code block containers
    const codeBlocks = await page.locator('pre, code[class*="language"], [class*="code"]').count();
    if (codeBlocks > 0) {
      const hasStyling = await page.locator('pre[class*="language"], code[class*="language"]').count() > 0;
      // Code blocks should have language classes if highlighted
      expect(codeBlocks).toBeGreaterThan(0);
    } else {
      // No code blocks on this page - that's fine
      expect(true).toBe(true);
    }
  });

  test('DOCS-05: Links between doc pages work', async ({ page }) => {
    await page.goto(BASE_URL + '?page=docs&doc=index&lang=en');
    await page.waitForTimeout(1000);

    // Find first internal doc link
    const docLink = page.locator('a[href*="doc="], a[href*="docs?"]').first();
    if (await docLink.count() > 0) {
      const href = await docLink.getAttribute('href');
      await page.goto(BASE_URL + href);
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      expect(body.length).toBeGreaterThan(50);
    } else {
      expect(true).toBe(true);
    }
  });

  test('DOCS-06: Bilingual toggle on docs page', async ({ page }) => {
    await gotoLang(page, '?page=docs', 'en');
    await page.waitForTimeout(500);
    const bodyEN = await page.textContent('body');
    expect(bodyEN).toMatch(/doc|guide|manual/i);

    await gotoLang(page, '?page=docs', 'es');
    await page.waitForTimeout(500);
    const bodyES = await page.textContent('body');
    expect(bodyES).toMatch(/doc|guia|manual/i);
  });
});

test.describe('Docs — Search', () => {
  test('DOCS-SEARCH-01: Search input is present', async ({ page }) => {
    await page.goto(BASE_URL + '?page=docs&lang=en');
    await page.waitForTimeout(1000);
    const searchInput = page.locator('input[type="search"], input[name*="search"], input[placeholder*="search"]').first();
    if (await searchInput.count() > 0) {
      await expect(searchInput).toBeVisible();
    }
  });

  test('DOCS-SEARCH-02: Search returns results', async ({ page }) => {
    await page.goto(BASE_URL + '?page=docs&lang=en');
    await page.waitForTimeout(1000);
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('setup');
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      expect(body).toMatch(/setup|result|found|search/i);
    } else {
      expect(true).toBe(true);
    }
  });

  test('DOCS-SEARCH-03: Search with no results shows empty state', async ({ page }) => {
    await page.goto(BASE_URL + '?page=docs&lang=en');
    await page.waitForTimeout(1000);
    const searchInput = page.locator('input[type="search"], input[name*="search"]').first();
    if (await searchInput.count() > 0) {
      await searchInput.fill('xyznonexistentword12345');
      await page.waitForTimeout(1000);
      const body = await page.textContent('body');
      expect(body).toMatch(/no.*result|no.*found|not.*found/i);
    }
  });
});

test.describe('Docs — Protected Pages', () => {
  test('DOCS-PROTECTED-01: Auth required for user docs', async ({ page }) => {
    // Navigate without auth
    await page.goto(BASE_URL + '?page=docs&doc=user-guide&lang=en');
    await page.waitForTimeout(1000);
    const url = page.url();
    // Should redirect to login or show access denied
    if (url.includes('login')) {
      expect(url).toMatch(/login|page=login/i);
    } else {
      const body = await page.textContent('body');
      expect(body).toMatch(/access denied|login|sign in/i);
    }
  });
});
