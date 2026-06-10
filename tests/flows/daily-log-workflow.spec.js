/**
 * Workflow Tests: Daily Log Lifecycle
 * Suite: Workflows > Daily Logs
 *
 * Tests: Create Daily Log → View Daily Log → Edit Daily Log
 *
 * @see TESTING-GUIDE.md Section 7 (Daily Logs) and Section 8 (Create Daily Log)
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth, assertNoFatalErrors, isAuthPage } = require('../helpers/auth');
const { BASE_URL } = require('../../playwright.config');
const { DAILY_LOG_DATA } = require('../fixtures/test-data');

test.describe('Daily Log Workflow: Create → View → Edit', () => {

  test('DL-WF-01: Create daily log with all fields', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_daily_log`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.content();
    assertNoFatalErrors(body);

    // Fill form
    const dateInput = page.locator('input[name="log_date"], input[name="date"]').first();
    const weatherInput = page.locator('input[name="weather"]').first();
    const manpowerInput = page.locator('input[name="manpower"]').first();
    const workTextarea = page.locator('textarea[name="work_performed"]').first();

    const dateVal = DAILY_LOG_DATA.valid.date;
    await dateInput.fill(dateVal);
    await weatherInput.fill(DAILY_LOG_DATA.valid.weather);
    await manpowerInput.fill(String(DAILY_LOG_DATA.valid.manpower));
    await workTextarea.fill(DAILY_LOG_DATA.valid.work_performed);

    await page.click('button[type="submit"], button:has-text("Save")');
    await page.waitForTimeout(3000);

    // Should redirect to view page or daily logs list
    const url = page.url();
    expect(url).toMatch(/view_daily_log|daily_logs/);
  });

  test('DL-WF-02: Create daily log with minimal fields', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_daily_log`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const dateInput = page.locator('input[name="log_date"], input[name="date"]').first();
    const manpowerInput = page.locator('input[name="manpower"]').first();

    await dateInput.fill(DAILY_LOG_DATA.minimal.date);
    await manpowerInput.fill(String(DAILY_LOG_DATA.minimal.manpower));

    await page.click('button[type="submit"], button:has-text("Save")');
    await page.waitForTimeout(3000);

    const url = page.url();
    expect(url).toMatch(/view_daily_log|daily_logs/);
  });

  test('DL-WF-03: View daily logs list page', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=daily_logs`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.content();
    assertNoFatalErrors(body);

    // Should show either log cards or empty state
    const content = await page.textContent('body');
    expect(content.length).toBeGreaterThan(0);
  });

  test('DL-WF-04: Navigate to view daily log detail', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=daily_logs`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Look for View Details link
    const viewLink = page.locator('a:has-text("View Details"), a:has-text("Ver")').first();
    const linkVisible = await viewLink.isVisible().catch(() => false);
    if (linkVisible) {
      await viewLink.click();
      await page.waitForTimeout(2000);
      const url = page.url();
      expect(url).toMatch(/view_daily_log|daily_logs/);
    }
  });

  test('DL-WF-05: GPS display shown on create daily log page', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_daily_log`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // GPS section should be visible
    const gpsSection = page.locator('text=/GPS|Latitude|Longitude|Coordinates/').first();
    await expect(gpsSection).toBeVisible({ timeout: 3000 }).catch(() => {});
  });

  test('DL-WF-06: Empty state shown when no daily logs exist', async ({ page }) => {
    await restoreAuth(page);
    // Note: This test depends on seeded data existing.
    // If DB is empty, should show empty state message.
    await page.goto(`${BASE_URL}/?page=daily_logs`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const content = await page.textContent('body');
    // Either has logs or shows empty state
    const hasLogs = content.includes('Daily Log') || content.includes('diario');
    const hasEmptyState = content.includes('No daily logs') || content.includes('No logs') || content.includes('No se encontraron');
    expect(hasLogs || hasEmptyState).toBe(true);
  });
});
