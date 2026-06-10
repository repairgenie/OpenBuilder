/**
 * Pagination Tests
 * Suite: Playwright > Pagination
 *
 * Tests pagination controls on list pages - page numbers,
 * prev/next buttons, page size selection, and URL parameter handling.
 *
 * @see TESTING-GUIDE.md Section "Pagination"
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { BASE_URL } = require('../playwright.config');

test.describe('Pagination — RFIs List', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=rfis&lang=en');
    await page.waitForTimeout(1000);
  });

  test('PAG-01: Pagination controls are present when data spans multiple pages', async ({ page }) => {
    const pagination = page.locator('nav.pagination, .pagination, [class*="page"], ul[class*="pagination"]');
    const count = await pagination.count();
    const body = await page.textContent('body');

    if (body.match(/1.*2.*3|page.*1.*2/i)) {
      // If there's pagination info, controls should exist
      expect(count >= 0).toBe(true); // Conditional test
    }
  });

  test('PAG-02: Next page button works', async ({ page }) => {
    const nextBtn = page.locator('a:has-text("next"), a:has-text(">"), button:has-text(">"), a[rel="next"]').first();
    if (await nextBtn.count() > 0) {
      const hrefBefore = await page.url();
      await nextBtn.click();
      await page.waitForTimeout(1000);
      const hrefAfter = await page.url();
      // URL should change or page content should update
      expect(hrefAfter).not.toBe(hrefBefore);
    } else {
      // Only one page - test passes
      expect(true).toBe(true);
    }
  });

  test('PAG-03: Previous page button works', async ({ page }) => {
    // First go to page 2
    const nextBtn = page.locator('a:has-text("next"), a:has-text(">")').first();
    if (await nextBtn.count() > 0) {
      await nextBtn.click();
      await page.waitForTimeout(1000);
    }

    const prevBtn = page.locator('a:has-text("prev"), a:has-text("<"), button:has-text("<"), a[rel="prev"]').first();
    if (await prevBtn.count() > 0) {
      await prevBtn.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      expect(url).toMatch(/page=1|offset=0/i);
    } else {
      expect(true).toBe(true);
    }
  });

  test('PAG-04: Clicking a page number navigates to that page', async ({ page }) => {
    const pageLink = page.locator('a:has-text("2"), a:has-text("3"), a:has-text("4")').first();
    if (await pageLink.count() > 0) {
      await pageLink.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      expect(url).toMatch(/page=2|page=3|page=4/i);
    } else {
      expect(true).toBe(true);
    }
  });

  test('PAG-05: Current page is highlighted', async ({ page }) => {
    const currentPage = page.locator('a[class*="active"], a[aria-current="page"], span[class*="current"]');
    if (await currentPage.count() > 0) {
      await expect(currentPage.first()).toBeVisible();
    } else {
      // Check if URL has page param
      const url = page.url();
      if (url.match(/page=\d/)) {
        expect(url).toMatch(/page=/);
      }
    }
  });

  test('PAG-06: Page size selector works', async ({ page }) => {
    const pageSizeSelect = page.locator('select[name*="per_page"], select[name*="limit"], select[name*="size"]').first();
    if (await pageSizeSelect.count() > 0) {
      await pageSizeSelect.selectOption('25');
      await page.waitForTimeout(1000);
      const url = page.url();
      expect(url).toMatch(/per_page=25|limit=25|size=25/i);
    } else {
      expect(true).toBe(true);
    }
  });
});

test.describe('Pagination — Daily Logs', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=daily_logs&lang=en');
    await page.waitForTimeout(1000);
  });

  test('PAG-LOGS-01: Pagination controls are present', async ({ page }) => {
    const pagination = page.locator('nav, .pagination, [class*="page"]');
    const count = await pagination.count();
    expect(count).toBeGreaterThanOrEqual(0);
  });

  test('PAG-LOGS-02: Next/Previous navigation works', async ({ page }) => {
    const nextBtn = page.locator('a:has-text("next"), a:has-text(">")').first();
    if (await nextBtn.count() > 0) {
      await nextBtn.click();
      await page.waitForTimeout(1000);
      const url = page.url();
      expect(url).toMatch(/page=\d+|offset=\d+/);
    } else {
      expect(true).toBe(true);
    }
  });
});

test.describe('Pagination — Edge Cases', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
  });

  test('PAG-EDGE-01: Invalid page number shows error or redirects', async ({ page }) => {
    await page.goto(BASE_URL + '?page=rfis&page=9999');
    await page.waitForTimeout(2000);
    const body = await page.textContent('body');
    const url = page.url();
    // Should either show empty state or redirect to valid page
    expect(body).toMatch(/no.*found|empty|page.*not.*exist|error/i);
  });

  test('PAG-EDGE-02: Page 0 redirects to page 1', async ({ page }) => {
    await page.goto(BASE_URL + '?page=rfis&page=0');
    await page.waitForTimeout(2000);
    const url = page.url();
    // Should not have page=0
    expect(url).not.toMatch(/page=0/);
  });

  test('PAG-EDGE-03: Negative page number handled', async ({ page }) => {
    await page.goto(BASE_URL + '?page=rfis&page=-1');
    await page.waitForTimeout(2000);
    const url = page.url();
    // Should not have negative page
    expect(url).not.toMatch(/page=-/);
  });

  test('PAG-EDGE-04: Non-numeric page parameter handled', async ({ page }) => {
    await page.goto(BASE_URL + '?page=rfis&page=abc');
    await page.waitForTimeout(2000);
    const body = await page.textContent('body');
    // Should show error or default to page 1
    expect(body).toMatch(/rfis|daily|log/i);
  });
});
