/**
 * Security Tests: Authentication Guards
 * Suite: Security > Auth Guards
 *
 * Verifies that all protected pages correctly redirect unauthenticated users
 * to the login page. This prevents unauthorized access to sensitive data.
 *
 * @see TESTING-GUIDE.md Section 3 (Authentication / Access Control)
 */
const { test, expect } = require('@playwright/test');
const { BASE_URL } = require('../../playwright.config');

/**
 * Helper: verify that accessing a URL without auth redirects to login.
 * @param {import('@playwright/test').Page} page
 * @param {string} pageName - Friendly name for test output
 * @param {string} url - Full URL to test
 */
async function assertRedirectsToLogin(page, pageName, url) {
  await page.goto(url, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(2000);

  const finalUrl = page.url();
  const body = await page.textContent('body');

  // Check: either redirected to login URL, or page is a login page
  const redirected = finalUrl.includes('page=login') ||
                    body.includes('Sign In') || body.includes('Iniciar Sesion') ||
                    body.includes('Email Address') || body.includes('Correo electronico');

  if (!redirected) {
    // Also check HTTP response status if possible
    console.log(`WARN: ${pageName} at ${url} did not redirect to login. URL: ${finalUrl}`);
  }

  expect(redirected).toBe(true);
  // Should NOT show protected content
  expect(finalUrl).not.toContain('page=rfis');
  expect(finalUrl).not.toContain('page=users');
}

test.describe('Auth Guards: Protected Pages Redirect to Login', () => {

  test('AG-01: /?page=rfis without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'RFIs', `${BASE_URL}/?page=rfis`);
  });

  test('AG-02: /?page=rfis&lang=es without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'RFIs ES', `${BASE_URL}/?page=rfis&lang=es`);
  });

  test('AG-03: /?page=users without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Users', `${BASE_URL}/?page=users`);
  });

  test('AG-04: /?page=users&lang=es without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Users ES', `${BASE_URL}/?page=users&lang=es`);
  });

  test('AG-05: /?page=daily_logs without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Daily Logs', `${BASE_URL}/?page=daily_logs`);
  });

  test('AG-06: /?page=daily_logs&lang=es without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Daily Logs ES', `${BASE_URL}/?page=daily_logs&lang=es`);
  });

  test('AG-07: /?page=budget without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Budget', `${BASE_URL}/?page=budget`);
  });

  test('AG-08: /?page=budget&lang=es without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Budget ES', `${BASE_URL}/?page=budget&lang=es`);
  });

  test('AG-09: /?page=tasks without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Tasks', `${BASE_URL}/?page=tasks`);
  });

  test('AG-10: /?page=tasks&lang=es without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Tasks ES', `${BASE_URL}/?page=tasks&lang=es`);
  });

  test('AG-11: /?page=dashboard without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Dashboard', `${BASE_URL}/?page=dashboard`);
  });

  test('AG-12: /?page=create_rfi without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Create RFI', `${BASE_URL}/?page=create_rfi`);
  });

  test('AG-13: /?page=create_daily_log without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Create Daily Log', `${BASE_URL}/?page=create_daily_log`);
  });

  test('AG-14: /?page=create_task without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Create Task', `${BASE_URL}/?page=create_task`);
  });

  test('AG-15: /?page=create_user without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Create User', `${BASE_URL}/?page=create_user`);
  });

  test('AG-16: /?page=roles without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Roles', `${BASE_URL}/?page=roles`);
  });

  test('AG-17: /?page=audit_logs without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'Audit Logs', `${BASE_URL}/?page=audit_logs`);
  });

  test('AG-18: /?page=api_keys without auth redirects to login', async ({ page }) => {
    await assertRedirectsToLogin(page, 'API Keys', `${BASE_URL}/?page=api_keys`);
  });
});

test.describe('Auth Guards: Login Page Accessibility', () => {

  test('AG-20: Login page is accessible without authentication', async ({ page }) => {
    await page.goto(`${BASE_URL}/?page=login`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);

    const url = page.url();
    const body = await page.textContent('body');
    const isLoginPage = url.includes('page=login') &&
                        (body.includes('Sign In') || body.includes('Iniciar Sesion') ||
                         body.includes('Email') || body.includes('Correo'));
    expect(isLoginPage).toBe(true);
  });

  test('AG-21: Login page in Spanish is accessible without auth', async ({ page }) => {
    await page.goto(`${BASE_URL}/?page=login&lang=es`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1500);

    const body = await page.textContent('body');
    const isSpanish = body.includes('Iniciar Sesion') || body.includes('Contrasena');
    expect(isSpanish).toBe(true);
  });
});

test.describe('Auth Guards: Public Pages Do Not Require Auth', () => {

  test('AG-25: Public pages do not redirect (landing, about, etc.)', async ({ page }) => {
    // Test common public page names
    const publicPages = ['home', 'landing', 'about', 'contact'];
    for (const pg of publicPages) {
      await page.goto(`${BASE_URL}/?page=${pg}`, { waitUntil: 'domcontentloaded' });
      await page.waitForTimeout(1500);
      const url = page.url();
      // These should either load or gracefully handle missing pages
      // They should NOT redirect to login
      const redirectedToLogin = url.includes('page=login') && !url.includes('page=home');
      expect(redirectedToLogin).toBe(false);
    }
  });
});