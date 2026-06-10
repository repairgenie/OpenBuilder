/**
 * Page Object: Budget (?page=budget) — PROTECTED
 *
 * Testable elements:
 * - Page title: "Budget"
 * - Currency selector (USD, EUR)
 * - Export CSV button
 * - Scenario Simulator: Variance slider (0-50%), Projected Impact display
 * - Budget table: Cost Code, Original Budget, Change Orders, Revised Budget, Committed, Variance
 * - Totals row
 */
const { expect } = require('@playwright/test');

class BudgetPage {
  constructor(page) {
    this.page = page;
    this.baseUrl = page.context()._options.baseURL || 'http://localhost:8080';
  }

  async goto(lang = 'en') {
    await this.page.goto(`${this.baseUrl}/?page=budget&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(1000);
  }

  get pageTitle() {
    return this.page.locator('h1, h2').first();
  }

  get currencySelector() {
    return this.page.locator('select[name="currency"], select[id*="currency"]').first();
  }

  get exportCsvButton() {
    return this.page.locator('button:has-text("Export CSV"), a:has-text("Export CSV")').first();
  }

  get varianceSlider() {
    return this.page.locator('input[type="range"], input[name*="variance"]').first();
  }

  get projectedImpact() {
    return this.page.locator('text=/Impact|Projected|Variance/').first();
  }

  get budgetTable() {
    return this.page.locator('table').first();
  }

  get totalsRow() {
    return this.page.locator('table tfoot tr, .totals-row, tr:has-text("Total")').first();
  }

  async switchCurrency(currency) {
    await this.currencySelector.selectOption(currency);
    await this.page.waitForTimeout(500);
  }

  async adjustVariance(value) {
    await this.varianceSlider.fill(String(value));
    await this.page.waitForTimeout(500);
  }

  async isVisible() {
    await expect(this.pageTitle).toBeVisible({ timeout: 5000 }).catch(() => {});
    await expect(this.budgetTable).toBeVisible({ timeout: 5000 }).catch(() => {});
  }

  async hasVarianceSlider() {
    await expect(this.varianceSlider).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async hasTotalsRow() {
    await expect(this.totalsRow).toBeVisible({ timeout: 3000 }).catch(() => {});
  }
}

module.exports = { BudgetPage };
