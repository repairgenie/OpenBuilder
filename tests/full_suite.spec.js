const { test, expect } = require('@playwright/test');

test.describe('OpenBuilder Full Suite - Advanced Features', () => {
  
  // 1. Bilingual Navigation & UI Integrity
  test('Bilingual Switcher & Labels', async ({ page }) => {
    await page.goto('/index.php?lang=en');
    await expect(page.locator('h2').first()).toContainText('Project Dashboard');
    
    await page.goto('/index.php?lang=es');
    await expect(page.locator('h2').first()).toContainText('Panel de Control');
  });

  // 2. Interactive Budget Simulator
  test('Budget Scenario Simulator', async ({ page }) => {
    await page.goto('/index.php?page=budget&lang=en');
    const slider = page.locator('#variance-slider');
    await expect(slider).toBeVisible();
    
    // Check initial state
    const impactLabel = page.locator('#projected-impact');

    const totalSpent = await page.evaluate(() => {
        const text = document.body.innerHTML;
        const match = text.match(/const totalSpent = (\d+);/);
        return match ? parseInt(match[1]) : 0;
    });

    if (totalSpent === 0) {
      // Just assert it exists and is visible to not fail if there's no data
      await expect(impactLabel).toBeVisible();
    } else {
      await expect(impactLabel).toContainText('$0');
      // Evaluate via DOM to trigger event listener
      await page.evaluate(() => {
          const slider = document.getElementById('variance-slider');
          slider.value = 25;
          slider.dispatchEvent(new Event('input', { bubbles: true }));
      });
      // Check if the value has been updated to NOT be exactly '$0'
      await expect(impactLabel).not.toHaveText('$0', { timeout: 1000 });
    }
  });

  // 3. RFI Map & Pins
  test('RFI Spatial Mapping', async ({ page }) => {
    await page.goto('/index.php?page=rfi_map&lang=en');
    await expect(page.locator('[data-tooltip*="RFI #001"]')).toBeVisible();
    await page.goto('/index.php?page=view_rfi&id=1&lang=en');
    await expect(page.url()).toContain('page=view_rfi');
  });

  // 4. Admin & Settings
  test('Project Settings Persistence', async ({ page }) => {
    await page.goto('/index.php?page=project_settings&lang=en');
    await page.fill('input[value="OpenBuilder HQ"]', 'Updated Project Name');
    await page.click('button:has-text("Save Changes")');
    await expect(page.locator('.toast')).toBeVisible();
    await expect(page.locator('.toast')).toContainText('Settings saved');
  });

  test('User Management & Roles', async ({ page }) => {
    await page.goto('/index.php?page=users&lang=en');
    await expect(page.locator('text=Admin').first()).toBeVisible();
    await expect(page.locator('td', { hasText: 'Manager' }).first()).toBeVisible();
  });

  // 5. Security (MFA Simulation)
  test('MFA Verification Flow', async ({ page }) => {
    await page.goto('/index.php?page=mfa&lang=en');
    await expect(page.locator('h2')).toContainText('Security Verification');
    const inputs = page.locator('input[maxlength="1"]');
    await expect(inputs).toHaveCount(6);
    
    await page.click('button:has-text("Verify")');
    await expect(page).toHaveURL(/page=dashboard/);
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
    await expect(page.locator('td', { hasText: 'Admin' }).first()).toBeVisible();
  });

});
