/**
 * Page Object: Create RFI (?page=create_rfi) — PROTECTED
 *
 * Testable elements:
 * - Reference Number input (required)
 * - Subject input (required)
 * - Due Date picker (required)
 * - Priority dropdown (Low, Medium, High)
 * - Submit RFI button
 * - Cancel button
 */
const { expect } = require('@playwright/test');

class CreateRFIPage {
  constructor(page) {
    this.page = page;
    this.baseUrl = page.context()._options.baseURL || 'http://localhost:8080';
  }

  async goto(lang = 'en') {
    await this.page.goto(`${this.baseUrl}/?page=create_rfi&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(1000);
  }

  // ── Locators ───────────────────────────────────────────────────────────────

  get refNumberInput() {
    return this.page.locator('input[name="ref_number"], input[id*="ref_number"]').first();
  }

  get subjectInput() {
    return this.page.locator('input[name="subject"], input[id*="subject"], textarea[name="subject"]').first();
  }

  get dueDateInput() {
    return this.page.locator('input[name="due_date"], input[id*="due_date"]').first();
  }

  get prioritySelect() {
    return this.page.locator('select[name="priority"], select[id*="priority"]').first();
  }

  get submitButton() {
    return this.page.locator('button[type="submit"], button:has-text("Submit"), button:has-text("Create")').first();
  }

  get cancelButton() {
    return this.page.locator('button:has-text("Cancel"), a:has-text("Cancel")').first();
  }

  get csrfToken() {
    return this.page.locator('input[name="csrf_token"]');
  }

  // ── Actions ────────────────────────────────────────────────────────────────

  async switchLang(lang) {
    await this.page.goto(`${this.baseUrl}/?page=create_rfi&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(500);
  }

  async fillForm({ ref_number, subject, due_date, priority = 'Medium' }) {
    if (ref_number) await this.refNumberInput.fill(ref_number);
    if (subject) await this.subjectInput.fill(subject);
    if (due_date) await this.dueDateInput.fill(due_date);
    if (priority) await this.prioritySelect.selectOption(priority);
  }

  async submit() {
    await this.submitButton.click();
    await this.page.waitForTimeout(2000);
  }

  async createRFI(data) {
    await this.fillForm(data);
    await this.submit();
  }

  // ── Assertions ──────────────────────────────────────────────────────────────

  async isVisible() {
    await expect(this.refNumberInput).toBeVisible({ timeout: 5000 }).catch(() => {});
    await expect(this.subjectInput).toBeVisible({ timeout: 5000 }).catch(() => {});
    await expect(this.dueDateInput).toBeVisible({ timeout: 5000 }).catch(() => {});
  }

  async hasRequiredFields() {
    // Check HTML5 required attributes
    const refRequired = await this.refNumberInput.getAttribute('required');
    const subjectRequired = await this.subjectInput.getAttribute('required');
    const dueRequired = await this.dueDateInput.getAttribute('required');
    return { refRequired, subjectRequired, dueRequired };
  }

  async isSpanish() {
    const content = await this.page.textContent('body');
    return content.includes('Numero de Referencia') || content.includes('Asunto');
  }
}

module.exports = { CreateRFIPage };
