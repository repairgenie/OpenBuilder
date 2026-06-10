/**
 * CSRF Protection Tests
 * Suite: Security > CSRF Protection
 *
 * Verifies that forms are protected by CSRF tokens and that
 * submissions without valid tokens are rejected.
 *
 * @see EDGE-TESTING-GUIDE.md Section 4 (Security Testing)
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { BASE_URL } = require('../playwright.config');

test.describe('CSRF Protection', () => {

  test.beforeEach(async ({ page }) => {
    // Login as admin before each test
    await page.goto(BASE_URL + '?page=login');
    await page.fill('input[name="email"]', 'admin@openbuilder.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    await page.waitForURL(/page=(?!login)/, { timeout: 10000 }).catch(() => {});
  });

  test('CSRF-01: Form submission without CSRF token should fail', async ({ page }) => {
    // Navigate to a form page
    await page.goto(BASE_URL + '?page=create_rfi');

    // Extract the CSRF token
    const csrfToken = await page.locator('input[name="csrf_token"]').getAttribute('value');

    // Fill out the form
    await page.fill('input[name="title"]', 'Test RFI');

    // Submit WITHOUT the CSRF token by manipulating the form
    await page.evaluate(() => {
      const form = document.querySelector('form');
      const tokenInput = form.querySelector('input[name="csrf_token"]');
      if (tokenInput) tokenInput.remove();
 form.submit();
    });

    // Should either show an error or redirect
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    // Either an error message or the form should not have submitted
    expect(body).toMatch(/csrf|forbidden|403|invalid|error/i);
  });

  test('CSRF-02: Form submission with invalid CSRF token should fail', async ({ page }) => {
    // Navigate to a form page
    await page.goto(BASE_URL + '?page=create_rfi');

    // Fill out the form with an invalid token
    await page.fill('input[name="csrf_token"]', 'invalid_token_12345');
    await page.fill('input[name="title"]', 'Test RFI');
    await page.click('button[type="submit"]');

    // Should show an error
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/csrf|forbidden|403|invalid|error/i);
  });

  test('CSRF-03: Form submission with valid CSRF token should succeed', async ({ page }) => {
    // Navigate to a form page
    await page.goto(BASE_URL + '?page=create_rfi');

    // Verify CSRF token exists
    const csrfToken = await page.locator('input[name="csrf_token"]').getAttribute('value');
    expect(csrfToken).toBeTruthy();
    expect(csrfToken.length).toBeGreaterThan(10);

    // Fill out the form with valid data
    await page.fill('input[name="title"]', 'Test RFI for CSRF Validation');
    await page.selectOption('select[name="priority"]', 'medium');
    await page.click('button[type="submit"]');

    // Should succeed and redirect
    await page.waitForURL(/page=rfis/, { timeout: 5000 }).catch(() => {});
    const url = page.url();
    expect(url).toMatch(/rfis|success|created/i);
  });

  test('CSRF-04: CSRF token is unique per session', async ({ page }) => {
    // Get token from first page load
    await page.goto(BASE_URL + '?page=create_rfi');
    const token1 = await page.locator('input[name="csrf_token"]').getAttribute('value');

    // Refresh the page - token should be the same
    await page.reload();
    const token2 = await page.locator('input[name="csrf_token"]').getAttribute('value');
    expect(token2).toBe(token1);

    // Open in a new incognito context (new session)
    const context2 = await browser.newContext();
    const page2 = await context2.newPage();
    await page2.goto(BASE_URL + '?page=login');
    await page2.fill('input[name="email"]', 'admin@openbuilder.com');
    await page2.fill('input[name="password"]', 'admin123');
    await page2.click('button[type="submit"]');
    await page2.waitForURL(/page=(?!login)/, { timeout: 10000 }).catch(() => {});
    await page2.goto(BASE_URL + '?page=create_rfi');
    const token3 = await page2.locator('input[name="csrf_token"]').getAttribute('value');

    // Tokens from different sessions should be different
    expect(token3).not.toBe(token1);
    await context2.close();
  });

  test('CSRF-05: Daily log form requires valid CSRF token', async ({ page }) => {
    await page.goto(BASE_URL + '?page=create_daily_log');

    // Submit with invalid token
    await page.fill('input[name="csrf_token"]', 'completely_invalid_token');
    await page.fill('input[name="log_date"]', '2026-06-10');
    await page.click('button[type="submit"]');

    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/csrf|forbidden|403|invalid|error/i);
  });

  test('CSRF-06: Task creation form requires valid CSRF token', async ({ page }) => {
    await page.goto(BASE_URL + '?page=tasks');

    // Try to submit task form with tampered token
    await page.fill('input[name="csrf_token"]', 'tampered_token');
    await page.click('button[type="submit"]');

    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/csrf|forbidden|403|invalid|error/i);
  });

  test('CSRF-07: User creation form requires valid CSRF token', async ({ page }) => {
    await page.goto(BASE_URL + '?page=users');

    // Try to create user with invalid token
    await page.fill('input[name="csrf_token"]', 'fake_token');
    await page.click('button[type="submit"]');

    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/csrf|forbidden|403|invalid|error/i);
  });

  test('CSRF-08: Budget form requires valid CSRF token', async ({ page }) => {
    await page.goto(BASE_URL + '?page=budget');

    // Try to add budget entry with invalid token
    await page.fill('input[name="csrf_token"]', 'invalid_budget_token');
    await page.click('button[type="submit"]');

    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/csrf|forbidden|403|invalid|error/i);
  });
});
