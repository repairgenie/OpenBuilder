// tests/full_suite.spec.js
const { test, expect } = require('@playwright/test');

test.describe('OpenBuilder Full Suite - Advanced Features', () => {
  
  // 1. Bilingual Navigation & UI Integrity
  test('Bilingual Switcher & Labels', async ({ page }) => {
    await page.goto('/index.php?lang=en');
    await expect(page.locator('h2')).toContainText('Project Dashboard');
    
    await page.goto('/index.php?lang=es');
    await expect(page.locator('h2')).toContainText('Panel de Control');
  });

  // 2. Interactive Budget Simulator
  test('Budget Scenario Simulator', async ({ page }) => {
    await page.goto('/index.php?page=budget&lang=en');
    const slider = page.locator('#variance-slider');
    await expect(slider).toBeVisible();
    
    // Simulate slider movement (Simulation via value attribute since it's a mock)
    await slider.evaluate(el => el.value = 25);
    await slider.dispatchEvent('input');
    
    const impactLabel = page.locator('#projected-impact');
    await expect(impactLabel).not.toContainText('$0');
  });

  // 3. RFI Map & Pins
  test('RFI Spatial Mapping', async ({ page }) => {
    await page.goto('/index.php?page=rfi_map&lang=en');
    await expect(page.locator('[data-tooltip*="RFI #001"]')).toBeVisible();
    await page.click('[data-tooltip*="RFI #001"]');
    await expect(page.url()).toContain('page=view_rfi');
  });

  // 4. Admin & Settings
  test('Project Settings Persistence', async ({ page }) => {
    await page.goto('/index.php?page=project_settings&lang=en');
    await page.fill('input[value="OpenBuilder HQ"]', 'Updated Project Name');
    await page.click('button:has-text("Save Changes")');
    await expect(page.locator('.toast')).toContainText('Settings saved');
  });

  test('User Management & Roles', async ({ page }) => {
    await page.goto('/index.php?page=users&lang=en');
    await expect(page.locator('text=Admin')).toBeVisible();
    await expect(page.locator('text=Manager')).toBeVisible();
  });

  // 5. Security (MFA Simulation)
  test('MFA Verification Flow', async ({ page }) => {
    await page.goto('/index.php?page=mfa&lang=en');
    await expect(page.locator('h2')).toContainText('Security Verification');
    const inputs = page.locator('input[maxlength="1"]');
    await expect(inputs).toHaveCount(6);
    
    await page.click('button:has-text("Verify")');
    await expect(page.url()).toContain('page=dashboard');
  });

  // 6. Mobile Usability
  test('Mobile Bottom Navigation', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 }); // iPhone SE size
    await page.goto('/index.php?lang=en');
    const bottomNav = page.locator('.fixed.bottom-0.lg\\:hidden');
    await expect(bottomNav).toBeVisible();
    await expect(bottomNav.locator('text=Home')).toBeVisible();
  });

  // 7. AI Elements (Visibility)
  test('AI Risk Heatmap & Predictions', async ({ page }) => {
    await page.goto('/index.php?lang=en');
    await expect(page.locator('h3:has-text("AI Risk Heatmap")')).toBeVisible();
    await expect(page.locator('h3:has-text("AI Schedule Prediction")')).toBeVisible();
  });

  // 8. Audit Logs
  test('Administrative Audit Trail', async ({ page }) => {
    await page.goto('/index.php?page=audit_logs&lang=en');
    await expect(page.locator('table')).toBeVisible();
    await expect(page.locator('text=Admin')).toBeVisible();
  });

});
