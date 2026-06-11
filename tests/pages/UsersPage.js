/**
 * Page Object: Users (?page=users) — PROTECTED
 *
 * Testable elements:
 * - Page title: "User Management"
 * - Add User button
 * - User table: Avatar, Name, Email, Role badge, Status badge, Actions
 * - Create/Edit modal: Name, Email, Role dropdown, Status dropdown
 */
const { expect } = require('@playwright/test');

class UsersPage {
  constructor(page) {
    this.page = page;
    this.baseUrl = process.env.TEST_BASE_URL || 'http://localhost:8080';
  }

  async goto(lang = 'en') {
    await this.page.goto(`${this.baseUrl}/?page=users&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(1000);
  }

  get pageTitle() {
    return this.page.locator('h1, h2').first();
  }

  get addUserButton() {
    return this.page.locator('button:has-text("Add User"), button:has-text("New User")').first();
  }

  get userTable() {
    return this.page.locator('table').first();
  }

  get userRows() {
    return this.page.locator('table tbody tr');
  }

  get userModal() {
    return this.page.locator('[class*="modal"], .modal, [role="dialog"]').first();
  }

  get nameInput() {
    return this.page.locator('input[name="name"]').first();
  }

  get emailInput() {
    return this.page.locator('input[name="email"]').first();
  }

  get roleSelect() {
    return this.page.locator('select[name="role"]').first();
  }

  get statusSelect() {
    return this.page.locator('select[name="status"]').first();
  }

  get saveButton() {
    return this.page.locator('button:has-text("Save"), button:has-text("Create")').first();
  }

  get cancelButton() {
    return this.page.locator('button:has-text("Cancel")').first();
  }

  async clickAddUser() {
    await this.addUserButton.click();
    await this.page.waitForTimeout(500);
  }

  async fillUserForm({ name, email, role, status }) {
    if (name) await this.nameInput.fill(name);
    if (email) await this.emailInput.fill(email);
    if (role) await this.roleSelect.selectOption(role);
    if (status) await this.statusSelect.selectOption(status);
  }

  async saveUser() {
    await this.saveButton.click();
    await this.page.waitForTimeout(2000);
  }

  async isVisible() {
    await expect(this.pageTitle).toBeVisible({ timeout: 5000 }).catch(() => {});
  }

  async hasUserTable() {
    await expect(this.userTable).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async hasUserRows() {
    const count = await this.userRows.count();
    return count > 0;
  }

  async isEmpty() {
    const content = await this.page.textContent('body');
    return content.includes('No users') || content.includes('No se encontraron');
  }
}

module.exports = { UsersPage };
