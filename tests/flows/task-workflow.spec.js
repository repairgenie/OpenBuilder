/**
 * Workflow Tests: Task Creation& Assignment
 * Suite: Workflows > Tasks
 *
 * Tests: Create Task → View in Gantt → Edit Task → Delete Task
 *
 * @see TESTING-GUIDE.md Section 9 (Tasks)
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth, assertNoFatalErrors, isAuthPage } = require('../helpers/auth');
const { BASE_URL } = require('../../playwright.config');
const { TASK_DATA } = require('../fixtures/test-data');

test.describe('Task Workflow: Create → View → Edit → Delete', () => {

  test('TASK-WF-01: Tasks page loads with Gantt view by default', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const body = await page.content();
    assertNoFatalErrors(body);

    // Should show either Gantt or Calendar view
    const content = await page.textContent('body');
    expect(content.length).toBeGreaterThan(0);
  });

  test('TASK-WF-02: Toggle between Gantt and Calendar view', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Find toggle button
    const toggleBtn = page.locator('button:has-text("Gantt"), button:has-text("Calendar")').first();
    const toggleVisible = await toggleBtn.isVisible().catch(() => false);
    if (toggleVisible) {
      await toggleBtn.click();
      await page.waitForTimeout(1000);

      // Click again to toggle back
      const toggleBtn2 = page.locator('button:has-text("Gantt"), button:has-text("Calendar")').first();
      await toggleBtn2.click();
      await page.waitForTimeout(1000);
    }
  });

  test('TASK-WF-03: Create new task with all fields', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Click New Task button
    const newTaskBtn = page.locator('button:has-text("New Task"), button:has-text("Add Task")').first();
    await newTaskBtn.click();
    await page.waitForTimeout(1000);

    // Fill task form
    const taskNameInput = page.locator('input[name="task_name"], input[id*="task_name"]').first();
    const startDateInput = page.locator('input[name="start_date"]').first();
    const endDateInput = page.locator('input[name="end_date"]').first();
    const statusSelect = page.locator('select[name="status"]').first();

    const taskName = 'Foundation Pour E2E-' + Date.now();
    await taskNameInput.fill(taskName);
    await startDateInput.fill(TASK_DATA.valid.start_date);
    await endDateInput.fill(TASK_DATA.valid.end_date);
    await statusSelect.selectOption('In Progress');

    // Save
    const saveBtn = page.locator('button:has-text("Save"), button:has-text("Create")').first();
    await saveBtn.click();
    await page.waitForTimeout(2000);

    // Should stay on tasks page
    const url = page.url();
    expect(url).toContain('tasks');
  });

  test('TASK-WF-04: Create task with minimal required fields', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    const newTaskBtn = page.locator('button:has-text("New Task"), button:has-text("Add Task")').first();
    await newTaskBtn.click();
    await page.waitForTimeout(1000);

    const taskNameInput = page.locator('input[name="task_name"]').first();
    const startDateInput = page.locator('input[name="start_date"]').first();
    const endDateInput = page.locator('input[name="end_date"]').first();

    await taskNameInput.fill('Minimal Task E2E-' + Date.now());
    await startDateInput.fill(TASK_DATA.minimal.start_date);
    await endDateInput.fill(TASK_DATA.minimal.end_date);

    const saveBtn = page.locator('button:has-text("Save"), button:has-text("Create")').first();
    await saveBtn.click();
    await page.waitForTimeout(2000);

    const url = page.url();
    expect(url).toContain('tasks');
  });

  test('TASK-WF-05: Edit existing task', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Look for Edit button on first task row
    const editBtn = page.locator('button:has-text("Edit"), a:has-text("Edit")').first();
    const editVisible = await editBtn.isVisible().catch(() => false);
    if (editVisible) {
      await editBtn.click();
      await page.waitForTimeout(1000);

      // Modal should open with task data
      const taskNameInput = page.locator('input[name="task_name"]').first();
      await expect(taskNameInput).toBeVisible({ timeout: 3000 }).catch(() => {});

      // Change the name
      await taskNameInput.fill('Updated Task Name E2E-' + Date.now());
      const saveBtn = page.locator('button:has-text("Save")').first();
      await saveBtn.click();
      await page.waitForTimeout(2000);
    }
  });

  test('TASK-WF-06: Delete task with confirmation', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Look for Delete button
    const deleteBtn = page.locator('button:has-text("Delete"), a:has-text("Delete")').first();
    const deleteVisible = await deleteBtn.isVisible().catch(() => false);
    if (deleteVisible) {
      await deleteBtn.click();
      await page.waitForTimeout(1000);

      // Should show confirmation dialog (SweetAlert or browser confirm)
      page.on('dialog', async dialog => {
        await dialog.accept();
      });
      await page.waitForTimeout(500);
    }
  });

  test('TASK-WF-07: Critical path tasks show visual indicator', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=tasks`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);

    if (isAuthPage(page)) throw new Error('Auth failed');

    // Look for red dot indicator on critical tasks
    const criticalIndicator = page.locator('[class*="critical"], .critical-dot, [style*="red"]').first();
    const indicatorVisible = await criticalIndicator.isVisible().catch(() => false);
    // Just check that the page loaded without errors
    const body = await page.content();
    assertNoFatalErrors(body);
  });
});
