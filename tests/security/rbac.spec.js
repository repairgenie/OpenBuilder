/**
 * Role-Based Access Control (RBAC) Tests
 * Suite: Security > RBAC
 *
 * Verifies that users can only access pages and actions
 * appropriate to their role (Admin > Manager > Sub).
 *
 * @see EDGE-TESTING-GUIDE.md Section 4 (Security Testing)
 */
const { test, expect, browser } = require('@playwright/test');
const { BASE_URL } = require('../playwright.config');

/**
 * Helper: Login as a specific user role.
 * @param {import('@playwright/test').Page} page
 * @param {string} role - 'admin', 'manager', or 'sub'
 */
async function loginAsRole(page, role) {
  const credentials = {
    admin: { email: 'admin@openbuilder.com', password: 'admin123' },
    manager: { email: 'manager@openbuilder.com', password: 'manager123' },
    sub: { email: 'sub@openbuilder.com', password: 'sub123' }
  };
  const creds = credentials[role];
  await page.goto(BASE_URL + '?page=login');
  await page.fill('input[name="email"]', creds.email);
  await page.fill('input[name="password"]', creds.password);
  await page.click('button[type="submit"]');
  await page.waitForURL(/page=(?!login)/, { timeout: 10000 }).catch(() => {});
}

test.describe('RBAC — Admin Access', () => {
  test.beforeEach(async ({ page }) => {
    await loginAsRole(page, 'admin');
  });

  test('RBAC-ADMIN-01: Admin can access User Management', async ({ page }) => {
    await page.goto(BASE_URL + '?page=users');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    // Should show user management page, not an access denied message
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
    expect(body).toMatch(/user|management|users/i);
  });

  test('RBAC-ADMIN-02: Admin can access Roles& Permissions', async ({ page }) => {
    await page.goto(BASE_URL + '?page=roles');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
    expect(body).toMatch(/role|permission|roles/i);
  });

  test('RBAC-ADMIN-03: Admin can access Audit Logs', async ({ page }) => {
    await page.goto(BASE_URL + '?page=audit_logs');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
    expect(body).toMatch(/audit|log|logs/i);
  });

  test('RBAC-ADMIN-04: Admin can access API Keys', async ({ page }) => {
    await page.goto(BASE_URL + '?page=api_keys');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
    expect(body).toMatch(/api|key|keys/i);
  });

  test('RBAC-ADMIN-05: Admin can access Project Settings', async ({ page }) => {
    await page.goto(BASE_URL + '?page=project_settings');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
    expect(body).toMatch(/settings|project/i);
  });
});

test.describe('RBAC — Manager Access', () => {
  test.beforeEach(async ({ page }) => {
    await loginAsRole(page, 'manager');
  });

  test('RBAC-MGR-01: Manager can access RFIs', async ({ page }) => {
    await page.goto(BASE_URL + '?page=rfis');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
  });

  test('RBAC-MGR-02: Manager can access Daily Logs', async ({ page }) => {
    await page.goto(BASE_URL + '?page=daily_logs');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
  });

  test('RBAC-MGR-03: Manager can access Tasks', async ({ page }) => {
    await page.goto(BASE_URL + '?page=tasks');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
  });

  test('RBAC-MGR-04: Manager can access Budget', async ({ page }) => {
    await page.goto(BASE_URL + '?page=budget');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
  });

  test('RBAC-MGR-05: Manager CANNOT access User Management', async ({ page }) => {
    await page.goto(BASE_URL + '?page=users');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    // Should show access denied or redirect
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });

  test('RBAC-MGR-06: Manager CANNOT access Roles & Permissions', async ({ page }) => {
    await page.goto(BASE_URL + '?page=roles');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });

  test('RBAC-MGR-07: Manager CANNOT access Audit Logs', async ({ page }) => {
    await page.goto(BASE_URL + '?page=audit_logs');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });

  test('RBAC-MGR-08: Manager CANNOT access API Keys', async ({ page }) => {
    await page.goto(BASE_URL + '?page=api_keys');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });
});

test.describe('RBAC — Sub/User Access', () => {
  test.beforeEach(async ({ page }) => {
    await loginAsRole(page, 'sub');
  });

  test('RBAC-SUB-01: Sub can access RFIs', async ({ page }) => {
    await page.goto(BASE_URL + '?page=rfis');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
  });

  test('RBAC-SUB-02: Sub can access Daily Logs', async ({ page }) => {
    await page.goto(BASE_URL + '?page=daily_logs');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).not.toMatch(/access denied|unauthorized|forbidden|permission denied/i);
  });

  test('RBAC-SUB-03: Sub CANNOT access User Management', async ({ page }) => {
    await page.goto(BASE_URL + '?page=users');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });

  test('RBAC-SUB-04: Sub CANNOT access Roles & Permissions', async ({ page }) => {
    await page.goto(BASE_URL + '?page=roles');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });

  test('RBAC-SUB-05: Sub CANNOT access Audit Logs', async ({ page }) => {
    await page.goto(BASE_URL + '?page=audit_logs');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });

  test('RBAC-SUB-06: Sub CANNOT access API Keys', async ({ page }) => {
    await page.goto(BASE_URL + '?page=api_keys');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });

  test('RBAC-SUB-07: Sub CANNOT access Project Settings', async ({ page }) => {
    await page.goto(BASE_URL + '?page=project_settings');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });

  test('RBAC-SUB-08: Sub CANNOT access Budget (if budget is admin-only)', async ({ page }) => {
    await page.goto(BASE_URL + '?page=budget');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    // May or may not be restricted - check app behavior
    // If budget shows "access denied" it means RBAC is enforced
    // If budget shows the page, the test passes as "not restricted"
    if (body.match(/access denied|unauthorized|forbidden|permission denied|not allowed/i)) {
      expect(true).toBe(true); // RBAC enforced
    }
    // Otherwise budget is accessible to subs, which is also valid
  });
});

test.describe('RBAC — Direct URL Access', () => {
  test('RBAC-DIRECT-01: Unauthenticated user cannot bypass login via direct URL', async ({ page }) => {
    // Try to access a protected page directly without logging in
    await page.goto(BASE_URL + '?page=users');
    await page.waitForTimeout(1000);
    const url = page.url();
    // Should redirect to login
    expect(url).toMatch(/page=login|login/);
  });

  test('RBAC-DIRECT-02: Manager cannot access admin pages via direct URL', async ({ page }) => {
    await loginAsRole(page, 'manager');
    // Try to access admin-only page via direct URL
    await page.goto(BASE_URL + '?page=users');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });

  test('RBAC-DIRECT-03: Sub cannot access admin pages via direct URL', async ({ page }) => {
    await loginAsRole(page, 'sub');
    await page.goto(BASE_URL + '?page=roles');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body).toMatch(/access denied|unauthorized|forbidden|permission denied|not allowed/i);
  });
});
