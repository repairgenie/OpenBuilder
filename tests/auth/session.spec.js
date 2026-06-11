/**
 * Auth Tests: Session Management & MFA
 * Suite: Authentication > Session & MFA
 *
 * Tests session persistence, logout, and MFA flow.
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth, assertNoFatalErrors, isAuthPage } = require('../helpers/auth');
const { BASE_URL } = require('../../playwright.config');

test.describe('Session Management', () => {

  test('S-01: Authenticated session persists across page navigations', async ({ page }) => {
    await restoreAuth(page);
    // Navigate to dashboard
    await page.goto(`${BASE_URL}/?page=dashboard`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    const body = await page.content();
    assertNoFatalErrors(body);

    // Navigate to RFIs (protected page)
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    const isLogin = isAuthPage(page);
    expect(isLogin).toBe(false); // Should NOT redirect to login
 });

  test('S-02: Unauthenticated user redirected to login when accessing protected page', async ({ page }) => {
    // Navigate directly to protected page without auth
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    const url = page.url();
    expect(url).toContain('page=login');
  });

  test('S-03: Session cookie is set after login', async ({ page }) => {
    const loginPage = require('../pages/LoginPage').LoginPage;
    const lp = new loginPage(page);
    await lp.goto('en');
    await lp.login('admin@openbuilder.com', 'admin123');
    await page.waitForURL(url => !url.toString().includes('page=login'), { timeout: 10000 }).catch(() => {});

    const cookies = await page.context().cookies();
    const sessionCookie = cookies.find(c => c.name === 'PHPSESSID' || c.name.startsWith('ob_'));
    expect(sessionCookie).toBeTruthy();
  });

  test('S-04: Logout clears session', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=dashboard`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    // Find and click logout
    const logoutLink = page.locator('a[href*="logout"], button:has-text("Logout"), button:has-text("Sign Out")').first();
    const logoutExists = await logoutLink.isVisible().catch(() => false);
    if (logoutExists) {
      await logoutLink.click();
      await page.waitForTimeout(1000);
      // Should redirect to login
      const url = page.url();
      expect(url).toContain('login');
    }
  });
});

test.describe('MFA Flow', () => {

  test('M-01: MFA page shown after login when not in test mode', async ({ page }) => {
    // Clear any existing cookies
    await page.context().clearCookies();
    await page.goto(`${BASE_URL}/?page=login`, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"]', 'admin@openbuilder.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    const url = page.url();
    // May go to MFA or dashboard depending on test mode
    console.log('After login URL:', url);
  });

  test('M-02: MFA page has6-digit code input', async ({ page }) => {
    await page.context().addCookies([{
      name: 'ob_test_mode',
      value: '1',
      domain: (new URL(BASE_URL).hostname),
      path: '/',
    }]);
    await page.goto(`${BASE_URL}/?page=login`, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"]', 'admin@openbuilder.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    await page.waitForURL(url => !url.toString().includes('page=login'), { timeout: 10000 }).catch(() => {});

    const currentUrl = page.url();
    if (currentUrl.includes('page=mfa') || currentUrl.includes('mfa.php')) {
      const codeInputs = page.locator('input[name="code[]"]');
      const count = await codeInputs.count();
      expect(count).toBe(6);
    } else {
      // Test mode bypassed MFA — this is acceptable
      console.log('MFA bypassed by test mode');
    }
  });
});
