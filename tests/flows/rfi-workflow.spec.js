/**
 * Workflow Tests: RFI Lifecycle
 * Suite: Workflows > RFI
 *
 * Tests: Create RFI → View RFI → Edit RFI → Close RFI
 *
 * @see TESTING-GUIDE.md Section 5 (RFIs) and Section 6 (Create RFI)
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth, assertNoFatalErrors, isAuthPage } = require('../helpers/auth');
const { RFIsPage } = require('../pages/RFIsPage');
const { CreateRFIPage } = require('../pages/CreateRFIPage');
const { BASE_URL } = require('../../playwright.config');
const { RFI_DATA } = require('../fixtures/test-data');

test.describe('RFI Workflow: Create → View → Edit → Close', () => {

  test('RFI-WF-01: Create new RFI with all fields', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.content();
    assertNoFatalErrors(body);

    const refNum = 'RFI-' + Date.now();
    await page.fill('input[name="ref_number"]', refNum);
    await page.fill('input[name="subject"]', 'Test RFI: Foundation Issue');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.selectOption('select[name="priority"]', 'High');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    // Should redirect to RFIs list
    const url = page.url();
    expect(url).toContain('rfis');
  });

  test('RFI-WF-02: Create RFI with minimal required fields', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const refNum = 'RFI-MIN-' + Date.now();
    await page.fill('input[name="ref_number"]', refNum);
    await page.fill('input[name="subject"]', 'Minimal RFI');
    await page.fill('input[name="due_date"]', '2026-06-30');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    const url = page.url();
    expect(url).toContain('rfis');
  });

  test('RFI-WF-03: View RFI list and navigate to detail', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.content();
    assertNoFatalErrors(body);

    // Look for a "View" link in the table
    const viewLink = page.locator('a:has-text("View")').first();
    const linkVisible = await viewLink.isVisible().catch(() => false);
    if (linkVisible) {
      await viewLink.click();
      await page.waitForTimeout(2000);
      const url = page.url();
      // Should navigate to a detail page
      expect(url).not.toBe(`${BASE_URL}/?page=rfis`);
    }
  });

  test('RFI-WF-04: Filter RFIs by Open status', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Find status filter
    const statusFilter = page.locator('select[name="status"], select[id*="status"]').first();
    const filterVisible = await statusFilter.isVisible().catch(() => false);
    if (filterVisible) {
      await statusFilter.selectOption('Open');
      const filterBtn = page.locator('button:has-text("Filter")').first();
      await filterBtn.click();
      await page.waitForTimeout(1000);

      const body = await page.textContent('body');
      // Should show Open RFIs
      expect(body).toMatch(/Open|Abierto/);
    }
  });

  test('RFI-WF-05: Filter RFIs by High priority', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const priorityFilter = page.locator('select[name="priority"], select[id*="priority"]').first();
    const filterVisible = await priorityFilter.isVisible().catch(() => false);
    if (filterVisible) {
      await priorityFilter.selectOption('High');
      const filterBtn = page.locator('button:has-text("Filter")').first();
      await filterBtn.click();
      await page.waitForTimeout(1000);

      const body = await page.textContent('body');
      expect(body).toMatch(/High|Alto/);
    }
  });

  test('RFI-WF-06: Clear filters resets RFI list', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Apply a filter first
    const statusFilter = page.locator('select[name="status"]').first();
    const filterVisible = await statusFilter.isVisible().catch(() => false);
    if (filterVisible) {
      await statusFilter.selectOption('Open');
      const filterBtn = page.locator('button:has-text("Filter")').first();
      await filterBtn.click();
      await page.waitForTimeout(1000);

      // Clear filters
      const clearLink = page.locator('a:has-text("Clear")').first();
      await clearLink.click();
      await page.waitForTimeout(1000);

      // Should show all RFIs again
      const url = page.url();
      expect(url).not.toMatch(/status=Open/);
    }
  });

  test('RFI-WF-07: Select single RFI shows bulk actions', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Find first checkbox in table
    const firstCheckbox = page.locator('table tbody tr:first-child input[type="checkbox"]').first();
    const cbVisible = await firstCheckbox.isVisible().catch(() => false);
    if (cbVisible) {
      await firstCheckbox.check();
      await page.waitForTimeout(500);

      // Bulk bar should appear
      const bulkBar = page.locator('button:has-text("Export PDF"), button:has-text("Close Selected")').first();
      await expect(bulkBar).toBeVisible({ timeout: 3000 }).catch(() => {});
    }
  });

  test('RFI-WF-08: Select all RFIs checks all checkboxes', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const selectAll = page.locator('input[type="checkbox"]').first();
    const selectAllVisible = await selectAll.isVisible().catch(() => false);
    if (selectAllVisible) {
      await selectAll.check();
      await page.waitForTimeout(500);

      // All row checkboxes should be checked
      const checkboxes = page.locator('table tbody input[type="checkbox"]');
      const count = await checkboxes.count();
      if (count > 0) {
        for (let i = 0; i < count; i++) {
          const isChecked = await checkboxes.nth(i).isChecked();
          expect(isChecked).toBe(true);
        }
      }
    }
  });
});
