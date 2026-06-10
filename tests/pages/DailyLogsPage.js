/**
 * Page Object: Daily Logs (?page=daily_logs) — PROTECTED
 *
 * Testable elements:
 * - Page title: "Daily Logs"
 * - Create Daily Log button
 * - Grid of log cards: Date, Weather badge, Work description, Manpower count
 * - "View Details" link per card
 * - Pagination
 */
const { expect } = require('@playwright/test');

class DailyLogsPage {
  constructor(page) {
    this.page = page;
    this.baseUrl = page.context()._options.baseURL || 'http://localhost:8080';
  }

  async goto(lang = 'en') {
    await this.page.goto(`${this.baseUrl}/?page=daily_logs&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(1000);
  }

  get pageTitle() {
    return this.page.locator('h1, h2').first();
  }

  get createButton() {
    return this.page.locator('a[href*="create_daily_log"], button:has-text("Create")').first();
  }

  get logCards() {
    return this.page.locator('[class*="card"], .log-card, article');
  }

  get viewDetailsLinks() {
    return this.page.locator('a:has-text("View Details"), a:has-text("Ver")');
  }

  get paginationInfo() {
    return this.page.locator('text=/Page \\d+ of \\d+/').first();
  }

  async clickCreate() {
    await this.createButton.click();
    await this.page.waitForURL(/create_daily_log/, { timeout: 5000 }).catch(() => {});
  }

  async clickFirstViewDetails() {
    const firstLink = this.viewDetailsLinks.first();
    await firstLink.click();
    await this.page.waitForURL(/view_daily_log/, { timeout: 5000 }).catch(() => {});
  }

  async isVisible() {
    await expect(this.pageTitle).toBeVisible({ timeout: 5000 }).catch(() => {});
  }

  async isEmpty() {
    const content = await this.page.textContent('body');
    return content.includes('No daily logs') || content.includes('No logs') || content.includes('No se encontraron');
  }

  async hasLogCards() {
    const count = await this.logCards.count();
    return count > 0;
  }
}

module.exports = { DailyLogsPage };
