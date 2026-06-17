/**
 * Edge Case Tests: Session Handling
 * Suite: Edge Cases > Session Handling
 *
 * Tests session lifecycle: timeout behavior, token expiration,
 * and what happens when the browser is closed and reopened.
 *
 * NOTE: Some of these tests depend on server-side session configuration.
 * We test the client-visible behavior (redirects, cookie state, auth prompts).
 *
 * @see TESTING-GUIDE.md Section 3 (Authentication) and Section 9 (Session)
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth, assertNoFatalErrors, isAuthPage } = require('../helpers/auth');
const { BASE_URL } = require('../../playwright.config');

test.describe('Session Handling: Timeout Behavior', () => {

  test('SH-TIME-01: Authenticated page is accessible immediately after login', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);

    // Should not redirect to login
    const url = page.url();
    const isAuthPageNow = isAuthPage(page);
    expect(isAuthPageNow).toBe(false);
    expect(url).not.toContain('page=login');
  });

  test('SH-TIME-02: Page navigation within session stays authenticated', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    // Navigate to RFIs
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    expect(isAuthPage(page)).toBe(false);

    // Navigate to users
    await page.goto(`${BASE_URL}/?page=users`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    expect(isAuthPage(page)).toBe(false);

    // Navigate to budget
    await page.goto(`${BASE_URL}/?page=budget`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    expect(isAuthPage(page)).toBe(false);
  });

  test('SH-TIME-03: Refresh within session keeps user authenticated', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    // Reload the page
    await page.reload({ waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);

    const url = page.url();
    expect(isAuthPage(page)).toBe(false);
    expect(url).not.toContain('page=login');
  });

  test('SH-TIME-04: Language toggle does not destroy session', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis&lang=en`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    // Switch to Spanish
    await page.goto(`${BASE_URL}/?page=rfis&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    const isStillAuth = !isAuthPage(page);
    expect(isStillAuth).toBe(true);
  });
});

test.describe('Session Handling: Token/Cookie Behavior', () => {

  test('SH-COOKIE-01: Session cookie is present when authenticated', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    const cookies = await page.context().cookies();
    const sessionCookie = cookies.find(c =>
      c.name.includes('PHPSESSID') || c.name.includes('session') || c.name.includes('token') || c.name.includes('auth')
    );
    expect(sessionCookie).toBeDefined();
  });

  test('SH-COOKIE-02: Fresh context (no cookies) redirects to login', async ({ page }) => {
    // Don't restore auth — use a fresh context with no session
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(2000);

    const url = page.url();
    // Should redirect to login
    const redirectedToLogin = url.includes('page=login') || isAuthPage(page);
    expect(redirectedToLogin).toBe(true);
  });

  test('SH-COOKIE-03: After logout, session cookies are cleared', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    // Find and click logout
    const logoutLink = page.locator('a:has-text("Logout"), a:has-text("Sign Out"), a:has-text("Cerrar Sesion"), a:has-text("Salir")').first();
    if (await logoutLink.count() > 0) {
      await logoutLink.click();
      await page.waitForTimeout(2000);
    } else {
      // Try navigating to logout endpoint directly
      await page.goto(`${BASE_URL}/?page=logout`, { waitUntil: 'domcontentloaded' });
      await page.waitForTimeout(2000);
    }

    // Session cookie should be gone
    const cookies = await page.context().cookies();
    const sessionCookie = cookies.find(c =>
      c.name.includes('PHPSESSID') || c.name.includes('session') || c.name.includes('token') || c.name.includes('auth')
    );
    // After logout, session cookie should either be gone or expired
    if (sessionCookie) {
      expect(sessionCookie.expires < Date.now() / 1000).toBe(true);
    }
  });

  test('SH-COOKIE-04: Navigating to protected page without session redirects to login', async ({ page }) => {
    // Fresh context — no auth
    const protectedPages = ['rfis', 'daily_logs', 'budget', 'users', 'tasks'];
    for (const pg of protectedPages) {
      await page.goto(`${BASE_URL}/?page=${pg}`, { waitUntil: 'domcontentloaded' });
      await page.waitForTimeout(1500);
      const url = page.url();  const redirectedToLogin = url.includes('page=login') || isAuthPage(page);
      expect(redirectedToLogin).toBe(true);
    }
  });
});

test.describe('Session Handling: Browser Close/Reopen', () => {

  test('SH-REOPEN-01: After closing and reopening context, session is invalid', async ({ page }) => {
    // This simulates what happens when browser is closed and reopened
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    // Get current cookies
    const cookiesBefore = await page.context().cookies();

    // Close the context (simulates browser close)
    await page.context().close();

    // Create a new context (simulates browser reopen)
    const browser = page.context().browser();
    const newContext = await browser.newContext();
    const newPage = await newContext.newPage();

    // Navigate to protected page with the new (empty) context
    await newPage.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await newPage.waitForTimeout(2000);

    const url = newPage.url();
    const redirectedToLogin = url.includes('page=login') || isAuthPage(newPage);
    expect(redirectedToLogin).toBe(true);

    await newContext.close();
  });

  test('SH-REOPEN-02: Saved cookies from previous session allow re-authentication', async ({ page }) => {
    // Restore auth (simulates having saved cookies)
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    const isAuth = !isAuthPage(page);
    expect(isAuth).toBe(true);

    // Get cookies
    const cookies = await page.context().cookies();

    // Close and reopen
    await page.context().close();
    const browser = page.context().browser();
    const newContext = await browser.newContext();
    const newPage = await newContext.newPage();

    // Re-add cookies manually (simulating cookie restore from storage)
    for (const cookie of cookies) {
      try {
        await newContext.addCookies([cookie]);
      } catch (e) {
        // Some cookies may not be valid for this context — skip
      }
    }

    // Navigate — should still be authenticated
    await newPage.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await newPage.waitForTimeout(1500);

    const isStillAuth = !isAuthPage(newPage);
    expect(isStillAuth).toBe(true);

    await newContext.close();
  });
});

test.describe('Session Handling: Multiple Tabs', () => {

  test('SH-TAB-01: Two tabs sharing session see consistent auth state', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    // Open a new tab
    const page2Promise = page.context().waitForEvent('page');
    await page.locator('body').click({ button: 'right' }).catch(() => {});
    // Use the more reliable approach
    const { Page } = require('playwright');
    const page2 = await page.context().newPage();

    await restoreAuth(page2);
    await page2.goto(`${BASE_URL}/?page=rfis`, { waitUntil: 'domcontentloaded' });
    await page2.waitForTimeout(1000);

    const tab1Auth = !isAuthPage(page);
    const tab2Auth = !isAuthPage(page2);

    expect(tab1Auth).toBe(true);
    expect(tab2Auth).toBe(true);

    await page2.close();
  });
});