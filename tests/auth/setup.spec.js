/**
 * Auth Setup - Authenticates once and saves session cookies for reuse.
 * All other test files depend on this to avoid repeated login overhead.
 *
 * Credentials:
 *   admin@openbuilder.com / admin123  (Admin role)
 *   manager@openbuilder.com / manager123  (Manager role)
 *   sub@openbuilder.com / sub123  (Subcontractor role)
 *
 * Test mode: Sets ob_test_mode=1 cookie so PHP bypasses MFA.
 */
const { test } = require('@playwright/test');
const fs = require('fs');
const path = require('path');
const { setTestModeCookie } = require('../helpers/auth');

const BASE_URL = process.env.TEST_BASE_URL || 'http://localhost:8080';
const AUTH_DIR = path.join(__dirname, '../.auth');
const COOKIE_FILE = path.join(AUTH_DIR, 'session.json');

test('authenticate-admin', async ({ page }) => {
  if (!fs.existsSync(AUTH_DIR)) fs.mkdirSync(AUTH_DIR, { recursive: true });

  // Signal test mode so PHP skips MFA and session regeneration
  await page.context().addCookies([{
    name: 'ob_test_mode',
    value: '1',
    domain: (new URL(BASE_URL).hostname),
    path: '/',
  }]);

  // ── Step 1: Load login page ───────────────────────────────────────────────
  await page.goto(`${BASE_URL}/?page=login`, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(500);

  const csrfToken = await page.locator('input[name="csrf_token"]').inputValue().catch(() => '');
  if (!csrfToken) throw new Error('No CSRF token found on login page');

  // ── Step 2: Submit credentials ───────────────────────────────────────────
  await page.fill('input[name="email"]', 'admin@openbuilder.com');
  await page.fill('input[name="password"]', 'admin123');
  await page.click('button[type="submit"]');

  // Wait for redirect away from login
  await page.waitForURL(url => !url.toString().includes('page=login'), { timeout: 10000 }).catch(() => {});

  // ── Step 3: Handle MFA if redirected ────────────────────────────────────
  const currentUrl = page.url();
  if (currentUrl.includes('page=mfa') || currentUrl.includes('mfa.php')) {
    // In test mode, MFA should be bypassed, but handle it gracefully
    const codeInputs = page.locator('input[name="code[]"]');
    const count = await codeInputs.count();
    if (count === 6) {
      // Try "000000" first
      for (let i = 0; i < 6; i++) {
        await codeInputs.nth(i).fill('0');
      }
      await page.click('button[type="submit"]');
      await page.waitForTimeout(1500);

      // If invalid, try to read the real code from PHP session file
      if (page.url().includes('invalid=1')) {
        const cookies = await page.context().cookies();
        const phpsessid = cookies.find(c => c.name === 'PHPSESSID');
        if (phpsessid) {
          const sessionPaths = ['/tmp', process.env.PHP_SESSION_DIR || '/tmp'];
          for (const sp of sessionPaths) {
            try {
              const sessionFile = path.join(sp, 'sess_' + phpsessid.value);
              if (fs.existsSync(sessionFile)) {
                const content = fs.readFileSync(sessionFile, 'utf8');
                const match = content.match(/mfa_code\|s:6:"(\d{6})"/);
                if (match) {
                  const realCode = match[1];
                  await page.goto(`${BASE_URL}/?page=mfa`, { waitUntil: 'domcontentloaded' });
                  await page.waitForTimeout(500);
                  const inputs = page.locator('input[name="code[]"]');
                  for (let i = 0; i < 6; i++) {
                    await inputs.nth(i).fill(realCode[i]);
                  }
                  await page.click('button[type="submit"]');
                  await page.waitForTimeout(1500);
                  break;
                }
              }
            } catch (e) { /* ignore */ }
          }
        }
      }
    }
  }

  // ── Step 4: Verify we're authenticated ───────────────────────────────────
  const finalUrl = page.url();
  if (finalUrl.includes('page=login')) {
    throw new Error('Auth failed: still on login page after credential submission');
  }

  // ── Step 5: Save session cookies ──────────────────────────────────────────
  const cookies = await page.context().cookies();
  fs.writeFileSync(COOKIE_FILE, JSON.stringify({ cookies, timestamp: Date.now() }, null, 2));
  console.log('✅ Auth cookie saved. Final URL:', finalUrl);
});

test('authenticate-manager', async ({ page }) => {
  // Similar flow for manager role (for RBAC tests)
  if (!fs.existsSync(AUTH_DIR)) fs.mkdirSync(AUTH_DIR, { recursive: true });

  await page.context().addCookies([{
    name: 'ob_test_mode',
    value: '1',
    domain: (new URL(BASE_URL).hostname),
    path: '/',
  }]);

  await page.goto(`${BASE_URL}/?page=login`, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(500);

  await page.fill('input[name="email"]', 'manager@openbuilder.com');
  await page.fill('input[name="password"]', 'manager123');
  await page.click('button[type="submit"]');
  await page.waitForURL(url => !url.toString().includes('page=login'), { timeout: 10000 }).catch(() => {});

  const cookies = await page.context().cookies();
  const managerFile = path.join(AUTH_DIR, 'manager-session.json');
  fs.writeFileSync(managerFile, JSON.stringify({ cookies, timestamp: Date.now() }, null, 2));
  console.log('✅ Manager auth cookie saved');
});

test('authenticate-sub', async ({ page }) => {
  // Similar flow for subcontractor role (for RBAC tests)
  if (!fs.existsSync(AUTH_DIR)) fs.mkdirSync(AUTH_DIR, { recursive: true });

  await page.context().addCookies([{
    name: 'ob_test_mode',
    value: '1',
    domain: (new URL(BASE_URL).hostname),
    path: '/',
  }]);

  await page.goto(`${BASE_URL}/?page=login`, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(500);

  await page.fill('input[name="email"]', 'sub@openbuilder.com');
  await page.fill('input[name="password"]', 'sub123');
  await page.click('button[type="submit"]');
  await page.waitForURL(url => !url.toString().includes('page=login'), { timeout: 10000 }).catch(() => {});

  const cookies = await page.context().cookies();
  const subFile = path.join(AUTH_DIR, 'sub-session.json');
  fs.writeFileSync(subFile, JSON.stringify({ cookies, timestamp: Date.now() }, null, 2));
  console.log('✅ Subcontractor auth cookie saved');
});
