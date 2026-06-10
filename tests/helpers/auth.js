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

  // Set test mode cookie
  await page.context().addCookies([{
    name: 'ob_test_mode',
    value: '1',
    domain: 'localhost',
    path: '/',
  }]);
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
  await page.context().addCookies([{
    name: 'ob_test_mode',
    value: '1',
    domain: 'localhost',
    path: '/',
  }]);
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
  await page.context().addCookies([{
    name: 'ob_test_mode',
    value: '1',
    domain: 'localhost',
    path: '/',
  }]);
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

module.exports = { restoreAuth, restoreManagerAuth, restoreSubAuth, assertNoFatalErrors, isAuthPage };
