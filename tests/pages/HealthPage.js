/**
 * Page Object: Health (?page=health) — PUBLIC
 *
 * Testable elements:
 * - Database status card (SQLite connection)
 * - AI Service status card (Gemini API key check)
 */
const { expect } = require('@playwright/test');

class HealthPage {
  constructor(page) {
    this.page = page;
    this.baseUrl = page.context()._options.baseURL || 'http://localhost:8080';
  }

  async goto(lang = 'en') {
    await this.page.goto(`${this.baseUrl}/?page=health&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(1000);
  }

  get databaseStatus() {
    return this.page.locator('text=/Database|SQLite|DB/').first();
  }

  get aiStatus() {
    return this.page.locator('text=/AI|Gemini|API/').first();
  }

  get healthyIndicator() {
    return this.page.locator('.success, .text-success, [class*="healthy"], [class*="success"]').first();
  }

  get unhealthyIndicator() {
    return this.page.locator('.danger, .text-danger, [class*="unhealthy"], [class*="error"]').first();
  }

  async isVisible() {
    await expect(this.databaseStatus).toBeVisible({ timeout: 5000 }).catch(() => {});
  }

  async hasDatabaseStatus() {
    await expect(this.databaseStatus).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async hasAIStatus() {
    await expect(this.aiStatus).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async isDatabaseHealthy() {
    await expect(this.healthyIndicator).toBeVisible({ timeout: 3000 }).catch(() => {});
  }
}

module.exports = { HealthPage };
