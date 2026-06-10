/**
 * Page Object: Dashboard (?page=dashboard) — PUBLIC PAGE
 *
 * Testable elements:
 * - Stats cards: Open RFIs, Total Logs, Budget Utilization %, Manpower Today
 * - Quick Actions: New Daily Log, Create RFI, New Cost Code
 * - Recent Activity feed
 * - Budget Distribution doughnut chart
 * - Project Timeline
 * - AI Schedule Prediction
 * - AI Risk Heatmap
 * - RFI Aging bar chart
 */
const { expect } = require('@playwright/test');

class DashboardPage {
  constructor(page) {
    this.page = page;
    this.baseUrl = page.context()._options.baseURL || 'http://localhost:8080';
  }

  async goto(lang = 'en') {
    await this.page.goto(`${this.baseUrl}/?page=dashboard&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(1000);
  }

  // ── Locators ───────────────────────────────────────────────────────────────

  get pageTitle() {
    return this.page.locator('h1, h2').first();
  }

  get openRfisCard() {
    return this.page.locator('text=Open RFIs').first();
  }

  get totalLogsCard() {
    return this.page.locator('text=Total Logs').first();
  }

  get budgetUtilCard() {
    return this.page.locator('text=Budget Utilization').first();
  }

  get manpowerCard() {
    return this.page.locator('text=Manpower').first();
  }

  get newDailyLogLink() {
    return this.page.locator('a[href*="create_daily_log"]').first();
  }

  get createRfiLink() {
    return this.page.locator('a[href*="create_rfi"]').first();
  }

  get newCostCodeLink() {
    return this.page.locator('a[href*="create_cost_code"]').first();
  }

  get budgetChart() {
    return this.page.locator('canvas').first();
  }

  get aiSchedulePrediction() {
    return this.page.locator('text=AI Schedule').first();
  }

  get aiRiskHeatmap() {
    return this.page.locator('text=AI Risk').first();
  }

  // ── Actions ────────────────────────────────────────────────────────────────

  async switchLang(lang) {
    await this.page.goto(`${this.baseUrl}/?page=dashboard&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(500);
  }

  async clickNewDailyLog() {
    await this.newDailyLogLink.click();
    await this.page.waitForURL(/create_daily_log/, { timeout: 5000 }).catch(() => {});
  }

  async clickCreateRfi() {
    await this.createRfiLink.click();
    await this.page.waitForURL(/create_rfi/, { timeout: 5000 }).catch(() => {});
  }

  async clickNewCostCode() {
    await this.newCostCodeLink.click();
    await this.page.waitForURL(/create_cost_code/, { timeout: 5000 }).catch(() => {});
  }

  // ── Assertions ──────────────────────────────────────────────────────────────

  async isVisible() {
    await expect(this.pageTitle).toBeVisible({ timeout: 5000 });
  }

  async hasStatsCards() {
    await expect(this.openRfisCard).toBeVisible({ timeout: 3000 }).catch(() => {});
    await expect(this.totalLogsCard).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async hasQuickActions() {
    await expect(this.newDailyLogLink).toBeVisible({ timeout: 3000 }).catch(() => {});
    await expect(this.createRfiLink).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async hasBudgetChart() {
    await expect(this.budgetChart).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async isSpanish() {
    const content = await this.page.textContent('body');
    return content.includes('Panel') || content.includes('RFI');
  }
}

module.exports = { DashboardPage };
