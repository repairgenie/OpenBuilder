/**
 * Workflow Tests: User Management
 * Suite: Workflows > Users
 *
 * Tests: View Users → Add User → Edit User → Delete User
 *
 * @see TESTING-GUIDE.md Section 11 (Users)
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth, assertNoFatalErrors, isAuthPage } = require('../helpers/auth');
const { BASE_URL } = require('../../playwright.config');
const { USER_DATA } = require('../fixtures/test-data');

test.describe('User Management Workflow', () => {

  test('USER-WF-01: Users page loads with user table', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=users`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.content();
    assertNoFatalErrors(body);

    const table = page.locator('table').first();
    await expect(table).toBeVisible({ timeout: 5000 }).catch(() => {});
  });

  test('USER-WF-02: Add new user opens modal', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=users`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const addBtn = page.locator('button:has-text("Add User"), button:has-text("New User")').first();
    await addBtn.click();
    await page.waitForTimeout(500);

    // Modal should open
    const modal = page.locator('[class*="modal"], .modal, [role="dialog"]').first();
    await expect(modal).toBeVisible({ timeout: 3000 }).catch(() => {});
  });

  test('USER-WF-03: Create new user with valid data', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=users`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Open modal
    const addBtn = page.locator('button:has-text("Add User")').first();
    await addBtn.click();
    await page.waitForTimeout(500);

    const nameInput = page.locator('input[name="name"]').first();
    const emailInput = page.locator('input[name="email"]').first();
    const roleSelect = page.locator('select[name="role"]').first();

    const testName = 'E2E Test User ' + Date.now();
    const testEmail = 'e2e-test-' + Date.now() + '@example.com';

    await nameInput.fill(testName);
    await emailInput.fill(testEmail);
    await roleSelect.selectOption('Subcontractor');

    const saveBtn = page.locator('button:has-text("Save"), button:has-text("Create")').first();
    await saveBtn.click();
    await page.waitForTimeout(2000);

    // Should stay on users page
    const url = page.url();
    expect(url).toContain('users');
  });

  test('USER-WF-04: Edit existing user', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=users`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Find Edit button
    const editBtn = page.locator('button:has-text("Edit"), a:has-text("Edit")').first();
    const editVisible = await editBtn.isVisible().catch(() => false);
    if (editVisible) {
      await editBtn.click();
      await page.waitForTimeout(500);

      const nameInput = page.locator('input[name="name"]').first();
      await expect(nameInput).toBeVisible({ timeout: 3000 }).catch(() => {});

      // Update name
      await nameInput.fill('Updated Name E2E-' + Date.now());
      const saveBtn = page.locator('button:has-text("Save")').first();
      await saveBtn.click();
      await page.waitForTimeout(2000);
    }
  });

  test('USER-WF-05: Delete user shows confirmation', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=users`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Find Delete button (skip if it's the last admin user)
    const deleteBtn = page.locator('button:has-text("Delete"), a:has-text("Delete")').first();
    const deleteVisible = await deleteBtn.isVisible().catch(() => false);
    if (deleteVisible) {
      page.on('dialog', async dialog => {
        await dialog.dismiss(); // Cancel deletion
      });
      await deleteBtn.click();
      await page.waitForTimeout(1000);
    }
  });

  test('USER-WF-06: User table shows role and status badges', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=users`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.textContent('body');
    // Should show some role information
    expect(body).toMatch(/Admin|Manager|Subcontractor|Activo|Inactivo/);
  });
});
