/**
 * Auth restoration helper.
 * Call restoreAuth(page) before navigating to a protected page
 * to inject saved session cookies.
 */
const fs = require('fs');
const path = require('path');

const AUTH_DIR = path.join(__dirname, '../.auth');
const COOKIE_FILE = path.join(AUTH_DIR, 'session.json');
const MANAGER_FILE = path.join(AUTH_DIR, 'manager-session.json');
const SUB_FILE = path.join(AUTH_DIR, 'sub-session.json');

/**
 * Derive the cookie domain from BASE_URL so tests work against any host
 * (localhost, 127.0.0.1, 192.168.8.147, etc.). Defaults to 'localhost'.
 */
function getCookieDomain() {
  const base = process.env.TEST_BASE_URL || 'http://localhost:8080';
  try {
    const u = new URL(base);
    return u.hostname || 'localhost';
  } catch {
    return 'localhost';
  }
}

/**
 * Inject the ob_test_mode=1 bypass cookie. Must be called BEFORE navigating
 * to a protected page or PHP will redirect to login.
 * @param {import('@playwright/test').Page} page
 */
async function setTestModeCookie(page) {
  await page.context().addCookies([{
    name: 'ob_test_mode',
    value: '1',
    domain: getCookieDomain(),
    path: '/',
  }]);
}

/**
 * Restore admin session cookies.
 * @param {import('@playwright/test').Page} page
 * @returns {Promise<boolean>} true if cookies were restored successfully
 */
async function restoreAuth(page) {
  if (!fs.existsSync(COOKIE_FILE)) {
    console.log('No admin cookie file found:', COOKIE_FILE);
    return false;
  }
  const { cookies } = JSON.parse(fs.readFileSync(COOKIE_FILE, 'utf8'));
  if (!cookies || cookies.length === 0) return false;

  // Set test mode cookie first
  await setTestModeCookie(page);
  await page.context().addCookies(cookies);
  return true;
}

/**
 * Restore manager session cookies.
 */
async function restoreManagerAuth(page) {
  if (!fs.existsSync(MANAGER_FILE)) return false;
  const { cookies } = JSON.parse(fs.readFileSync(MANAGER_FILE, 'utf8'));
  if (!cookies || cookies.length === 0) return false;
  await setTestModeCookie(page);
  await page.context().addCookies(cookies);
  return true;
}

/**
 * Restore subcontractor session cookies.
 */
async function restoreSubAuth(page) {
  if (!fs.existsSync(SUB_FILE)) return false;
  const { cookies } = JSON.parse(fs.readFileSync(SUB_FILE, 'utf8'));
  if (!cookies || cookies.length === 0) return false;
  await setTestModeCookie(page);
  await page.context().addCookies(cookies);
  return true;
}

/**
 * Assert no PHP fatal errors in page content.
 * @param {string} bodyText
 */
function assertNoFatalErrors(bodyText) {
  const fatalPatterns = [
    '<?php', 'Fatal error', 'Parse error',
    'Call to undefined', 'SQLSTATE', 'Warning: ',
    'Notice:', 'Deprecated:',
  ];
  for (const pat of fatalPatterns) {
    if (bodyText.includes(pat)) {
      throw new Error(`Fatal error pattern detected: ${pat}`);
    }
  }
}

/**
 * Check if the current page is a login/auth page.
 * @param {import('@playwright/test').Page} page
 */
function isAuthPage(page) {
  return page.url().includes('page=login') || page.url().includes('mfa.php');
}

module.exports = { restoreAuth, restoreManagerAuth, restoreSubAuth, setTestModeCookie, getCookieDomain, assertNoFatalErrors, isAuthPage };
