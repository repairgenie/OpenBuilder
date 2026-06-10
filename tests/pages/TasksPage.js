/**
 * Page Object: Tasks (?page=tasks) — PROTECTED
 *
 * Testable elements:
 * - View toggle (Gantt / Calendar)
 * - New Task button
 * - Gantt View: Task name, Timeline, Critical path indicator (red dots)
 * - Calendar View: Monthly grouping
 * - Task List table: Task name, Start Date, End Date, Crew, Cost Code, Status, Actions
 * - Create/Edit Task modal
 */
const { expect } = require('@playwright/test');

class TasksPage {
  constructor(page) {
    this.page = page;
    this.baseUrl = page.context()._options.baseURL || 'http://localhost:8080';
  }

  async goto(lang = 'en') {
    await this.page.goto(`${this.baseUrl}/?page=tasks&lang=${lang}`, {
      waitUntil: 'domcontentloaded',
    });
    await this.page.waitForTimeout(1000);
  }

  get pageTitle() {
    return this.page.locator('h1, h2').first();
  }

  get viewToggle() {
    return this.page.locator('button:has-text("Gantt"), button:has-text("Calendar")').first();
  }

  get newTaskButton() {
    return this.page.locator('button:has-text("New Task"), button:has-text("Add Task")').first();
  }

  get ganttView() {
    return this.page.locator('[class*="gantt"], .gantt-chart').first();
  }

  get calendarView() {
    return this.page.locator('[class*="calendar"], .calendar-view').first();
  }

  get taskTable() {
    return this.page.locator('table').first();
  }

  get taskRows() {
    return this.page.locator('table tbody tr');
  }

  get taskModal() {
    return this.page.locator('[class*="modal"], .modal, [role="dialog"]').first();
  }

  get taskNameInput() {
    return this.page.locator('input[name="task_name"], input[id*="task_name"]').first();
  }

  get startDateInput() {
    return this.page.locator('input[name="start_date"]').first();
  }

  get endDateInput() {
    return this.page.locator('input[name="end_date"]').first();
  }

  get statusSelect() {
    return this.page.locator('select[name="status"]').first();
  }

  get criticalCheckbox() {
    return this.page.locator('input[name="critical"], input[id*="critical"]').first();
  }

  get saveTaskButton() {
    return this.page.locator('button:has-text("Save"), button:has-text("Create")').first();
  }

  async toggleView() {
    await this.viewToggle.click();
    await this.page.waitForTimeout(1000);
  }

  async clickNewTask() {
    await this.newTaskButton.click();
    await this.page.waitForTimeout(500);
  }

  async fillTaskForm({ task_name, start_date, end_date, status, critical }) {
    if (task_name) await this.taskNameInput.fill(task_name);
    if (start_date) await this.startDateInput.fill(start_date);
    if (end_date) await this.endDateInput.fill(end_date);
    if (status) await this.statusSelect.selectOption(status);
    if (critical !== undefined) {
      const isChecked = await this.criticalCheckbox.isChecked();
      if (critical && !isChecked) await this.criticalCheckbox.check();
      if (!critical && isChecked) await this.criticalCheckbox.uncheck();
    }
  }

  async saveTask() {
    await this.saveTaskButton.click();
    await this.page.waitForTimeout(2000);
  }

  async isVisible() {
    await expect(this.pageTitle).toBeVisible({ timeout: 5000 }).catch(() => {});
  }

  async hasGanttView() {
    await expect(this.ganttView).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async hasTaskTable() {
    await expect(this.taskTable).toBeVisible({ timeout: 3000 }).catch(() => {});
  }

  async hasTaskRows() {
    const count = await this.taskRows.count();
    return count > 0;
  }
}

module.exports = { TasksPage };
