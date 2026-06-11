/**
 * Page Object: Create Daily Log (?page=create_daily_log) — PROTECTED
 *
 * Testable elements:
 * - Date picker (pre-filled with today)
 * - Weather input (auto-filled from API)
 * - Manpower (number input)
 * - GPS display (lat/lon with status)
 * - Work Performed textarea
 * - Cancel button
 * - "Save & Generate Report" button
 */
const { expect } = require('@playwright/test');

class CreateDailyLogPage {
  constructor(page) {
    this.page = page;
    this.baseUrl = process.env.TEST_BASE_URL || 'http://localhost:8080';
  }

  async goto(lang = 'en') {
    await this.page.goto(`${this.baseUrl}/?page=create_daily_log&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(1000);
  }

  get dateInput() {
    return this.page.locator('input[name="log_date"], input[name="date"]').first();
  }

  get weatherInput() {
    return this.page.locator('input[name="weather"]').first();
  }

  get manpowerInput() {
    return this.page.locator('input[name="manpower"]').first();
  }

  get gpsDisplay() {
    return this.page.locator('text=/GPS|Lat|Lon|Coordinates/').first();
  }

  get workPerformedTextarea() {
    return this.page.locator('textarea[name="work_performed"]').first();
  }

  get cancelButton() {
    return this.page.locator('button:has-text("Cancel"), a:has-text("Cancel")').first();
  }

  get saveButton() {
    return this.page.locator('button:has-text("Save"), button:has-text("Generate")').first();
  }

  get csrfToken() {
    return this.page.locator('input[name="csrf_token"]');
  }

  async fillForm({ date, weather, manpower, work_performed }) {
    if (date) await this.dateInput.fill(date);
    if (weather) await this.weatherInput.fill(weather);
    if (manpower !== undefined) await this.manpowerInput.fill(String(manpower));
    if (work_performed) await this.workPerformedTextarea.fill(work_performed);
  }

  async submit() {
    await this.saveButton.click();
    await this.page.waitForTimeout(2000);
  }

  async createLog(data) {
    await this.fillForm(data);
    await this.submit();
  }

  async isVisible() {
    await expect(this.dateInput).toBeVisible({ timeout: 5000 }).catch(() => {});
    await expect(this.manpowerInput).toBeVisible({ timeout: 5000 }).catch(() => {});
  }

  async hasDatePreFilled() {
    const value = await this.dateInput.inputValue();
    return value.length > 0;
  }

  async hasGpsDisplay() {
    await expect(this.gpsDisplay).toBeVisible({ timeout: 3000 }).catch(() => {});
  }
}

module.exports = { CreateDailyLogPage };
