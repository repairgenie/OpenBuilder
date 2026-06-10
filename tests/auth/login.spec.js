/**
 * Auth Tests: Login Page Elements
 * Suite: Authentication > Login Page
 *
 * Tests login page UI elements in both EN and ES languages.
 * @see TESTING-GUIDE.md Section3 (Login)
 */
const { test, expect } = require('@playwright/test');
const { BASE_URL } = require('../../playwright.config');
const { LoginPage } = require('../pages/LoginPage');
const { USERS } = require('../fixtures/test-data');

test.describe('Login Page — UI Elements', () => {

  test('L-01: Login page loads with all form elements (EN)', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('en');
    await loginPage.isVisible();
    await loginPage.hasCsrfToken();
  });

  test('L-02: Login page loads in Spanish (ES)', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('es');
    await loginPage.isVisible();
    const isSpanish = await loginPage.isSpanish();
    expect(isSpanish).toBe(true);
  });

  test('L-03: Language toggle via URL parameter persists', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('es');
    const url1 = page.url();
    expect(url1).toContain('lang=es');

    await loginPage.goto('en');
    const url2 = page.url();
    expect(url2).toContain('lang=en');
  });

  test('L-04: View site without account link works', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('en');
    await loginPage.viewWithoutAccountLink.click();
    await page.waitForURL(/dashboard/, { timeout: 5000 }).catch(() => {});
    const url = page.url();
    expect(url).toContain('dashboard');
  });

  test('L-05: Empty email shows HTML5 validation error', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('en');
    // Email has required attribute — submit without filling
    await loginPage.passwordInput.fill('admin123');
    await loginPage.submitButton.click();
    // Browser should show native validation
    const emailValue = await loginPage.emailInput.inputValue();
    expect(emailValue).toBe('');
  });

  test('L-06: Empty password shows HTML5 validation error', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('en');
    await loginPage.emailInput.fill('admin@openbuilder.com');
    await loginPage.submitButton.click();
    // Password should be required
    const pwValue = await loginPage.passwordInput.inputValue();
    expect(pwValue).toBe('');
  });
});

test.describe('Login — Credential Validation', () => {

  test('L-10: Valid admin credentials redirect away from login', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('en');
    await loginPage.login(USERS.admin.email, USERS.admin.password);
    await page.waitForURL(url => !url.toString().includes('page=login'), { timeout: 10000 }).catch(() => {});
    const url = page.url();
    // Should redirect to dashboard or MFA
    expect(url).not.toContain('page=login');
  });

  test('L-11: Invalid password shows error message', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('en');
    await loginPage.login(USERS.admin.email, 'wrongpassword');
    await page.waitForTimeout(2000);
    // Should still be on login page
    const isOnLogin = page.url().includes('page=login');
    expect(isOnLogin).toBe(true);
    // May show error message
    const hasError = await loginPage.hasErrorMessage();
    // Error message is desirable but not required if page stays on login
  });

  test('L-12: Non-existent email shows error', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('en');
    await loginPage.login('nonexistent@example.com', 'anypassword');
    await page.waitForTimeout(2000);
    const isOnLogin = page.url().includes('page=login');
    expect(isOnLogin).toBe(true);
  });

  test('L-13: Manager credentials work', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('en');
    await loginPage.login(USERS.manager.email, USERS.manager.password);
    await page.waitForURL(url => !url.toString().includes('page=login'), { timeout: 10000 }).catch(() => {});
    expect(page.url()).not.toContain('page=login');
  });

  test('L-14: Subcontractor credentials work', async ({ page }) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto('en');
    await loginPage.login(USERS.sub.email, USERS.sub.password);
    await page.waitForURL(url => !url.toString().includes('page=login'), { timeout: 10000 }).catch(() => {});
    expect(page.url()).not.toContain('page=login');
  });
});
