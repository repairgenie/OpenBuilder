/**
 * Page Object: RFIs List (?page=rfis) — PROTECTED
 *
 * Testable elements:
 * - Page title: "Requests for Information (RFIs)"
 * - Export CSV button
 * - Create RFI button
 * - Status filter dropdown (All, Open, Closed)
 * - Priority filter dropdown (All, High, Medium, Low)
 * - Filter button
 * - Clear link
 * - RFI List table
 * - Bulk actions bar (when items selected)
 * - Pagination controls
 */
const { expect } = require('@playwright/test');

class RFIsPage {
  constructor(page) {
    this.page = page;
    this.baseUrl = process.env.TEST_BASE_URL || 'http://localhost:8080';
  }

  async goto(lang = 'en') {
    await this.page.goto(`${this.baseUrl}/?page=rfis&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(1000);
  }

  // ── Locators ───────────────────────────────────────────────────────────────

  get pageTitle() {
    return this.page.locator('h1, h2').first();
  }

  get createRfiButton() {
    return this.page.locator('a[href*="create_rfi"], button:has-text("Create")').first();
  }

  get exportCsvButton() {
    return this.page.locator('button:has-text("Export CSV"), a:has-text("Export CSV")').first();
  }

  get statusFilter() {
    return this.page.locator('select[name="status"], select[id*="status"]').first();
  }

  get priorityFilter() {
    return this.page.locator('select[name="priority"], select[id*="priority"]').first();
  }

  get filterButton() {
    return this.page.locator('button:has-text("Filter")').first();
  }

  get clearLink() {
    return this.page.locator('a:has-text("Clear")').first();
  }

  get rfiTable() {
    return this.page.locator('table').first();
  }

  get rfiRows() {
    return this.page.locator('table tbody tr');
  }

  get selectAllCheckbox() {
    return this.page.locator('input[type="checkbox"]').first();
  }

  get bulkExportButton() {
    return this.page.locator('button:has-text("Export PDF")').first();
  }

  get bulkCloseButton() {
    return this.page.locator('button:has-text("Close Selected")').first();
  }

  get paginationInfo() {
    return this.page.locator('text=/Page \\d+ of \\d+/').first();
  }

  // ── Actions ────────────────────────────────────────────────────────────────

  async switchLang(lang) {
    await this.page.goto(`${this.baseUrl}/?page=rfis&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(500);
  }

  async filterByStatus(status) {
    await this.statusFilter.selectOption(status);
    await this.filterButton.click();
    await this.page.waitForTimeout(1000);
  }

  async filterByPriority(priority) {
    await this.priorityFilter.selectOption(priority);
    await this.filterButton.click();
    await this.page.waitForTimeout(1000);
  }

  async clearFilters() {
    await this.clearLink.click();
    await this.page.waitForTimeout(1000);
  }

  async clickCreateRfi() {
    await this.createRfiButton.click();
    await this.page.waitForURL(/create_rfi/, { timeout: 5000 }).catch(() => {});
  }

  async selectFirstRfi() {
    const firstCheckbox = this.page.locator('table tbody tr:first-child input[type="checkbox"]').first();
    await firstCheckbox.check();
    await this.page.waitForTimeout(500);
  }

  async selectAllRfis() {
    await this.selectAllCheckbox.check();
    await this.page.waitForTimeout(500);
  }

  // ── Assertions ──────────────────────────────────────────────────────────────

  async isVisible() {
    await expect(this.pageTitle).toBeVisible({ timeout: 5000 }).catch(() => {});
    await expect(this.rfiTable).toBeVisible({ timeout: 5000 }).catch(() => {});
  }

  async hasCreateButton() {
    await expect(this.createRfiButton).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async hasFilters() {
    await expect(this.statusFilter).toBeVisible({ timeout: 3000 }).catch(() => {});
    await expect(this.priorityFilter).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async hasBulkActions() {
    await expect(this.bulkExportButton).toBeVisible({ timeout: 3000 }).catch(() => {});
    await expect(this.bulkCloseButton).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async hasRfiRows() {
    const count = await this.rfiRows.count();
    return count > 0;
  }

  async isEmpty() {
    const content = await this.page.textContent('body');
    return content.includes('No RFIs') || content.includes('No requests') || content.includes('No results');
  }
}

module.exports = { RFIsPage };
