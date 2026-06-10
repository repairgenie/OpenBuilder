/**
 * Page Object: Login Page (?page=login)
 *
 * Testable elements:
 * - Email input field
 * - Password input field
 * - Submit button ("Iniciar Sesion / Sign In")
 * - "View site without account" link
 * - Language toggle (via URL parameter)
 * - CSRF token hidden field
 * - Error message display area
 */
const { expect } = require('@playwright/test');

class LoginPage {
  constructor(page) {
    this.page = page;
    this.baseUrl = page.context()._options.baseURL || 'http://localhost:8080';
  }

  /** Navigate to the login page */
  async goto(lang = 'en') {
    await this.page.goto(`${this.baseUrl}/?page=login&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(500);
  }

  // ── Locators ───────────────────────────────────────────────────────────────

  get emailInput() {
    return this.page.locator('input[name="email"]');
  }

  get passwordInput() {
    return this.page.locator('input[name="password"]');
  }

  get submitButton() {
    return this.page.locator('button[type="submit"]');
  }

  get csrfToken() {
    return this.page.locator('input[name="csrf_token"]');
  }

  get viewWithoutAccountLink() {
    return this.page.locator('a[href*="page=dashboard"]').first();
  }

  get errorMessage() {
    return this.page.locator('.text-danger, .error, [class*="error"]').first();
  }

  // ── Actions ────────────────────────────────────────────────────────────────

  /** Fill email and password fields */
  async fillCredentials(email, password) {
    await this.emailInput.fill(email);
    await this.passwordInput.fill(password);
  }

  /** Submit the login form */
  async submit() {
    await this.submitButton.click();
  }

  /** Perform full login flow */
  async login(email, password) {
    await this.fillCredentials(email, password);
    await this.submit();
 }

  /** Switch language via URL */
  async switchLang(lang) {
    await this.page.goto(`${this.baseUrl}/?page=login&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(500);
  }

  // ── Assertions ──────────────────────────────────────────────────────────────

  /** Verify the login form is visible */
  async isVisible() {
    await expect(this.emailInput).toBeVisible();
    await expect(this.passwordInput).toBeVisible();
    await expect(this.submitButton).toBeVisible();
  }

  /** Verify CSRF token is present */
  async hasCsrfToken() {
    const token = await this.csrfToken.inputValue();
    expect(token).toBeTruthy();
    expect(token.length).toBeGreaterThan(10);
  }

  /** Verify error message is shown (for invalid credentials) */
  async hasErrorMessage() {
    await expect(this.errorMessage).toBeVisible({ timeout: 5000 }).catch(() => {});
    const text = await this.errorMessage.textContent().catch(() => '');
    return text.length > 0;
  }

  /** Verify page is in Spanish mode */
  async isSpanish() {
    const content = await this.page.textContent('body');
    return content.includes('Iniciar Sesion') || content.includes('Contrasena');
  }

  /** Verify page is in English mode */
  async isEnglish() {
    const content = await this.page.textContent('body');
    return content.includes('Sign In') || content.includes('Email Address');
  }
}

module.exports = { LoginPage };
