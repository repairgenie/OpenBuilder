# Instructions

- Following Playwright test failed.
- Explain why, be concise, respect Playwright best practices.
- Provide a snippet of code with the fix, if possible.

# Test info

- Name: tests/full_suite.spec.js >> OpenBuilder Full Suite - Advanced Features >> User Management & Roles
- Location: tests/full_suite.spec.js:61:3

# Error details

```
Error: page.goto: Protocol error (Page.navigate): Cannot navigate to invalid URL
Call log:
  - navigating to "/index.php?page=users&lang=en", waiting until "load"

```

# Test source

```ts
  1   | const { test, expect } = require('@playwright/test');
  2   |
  3   | test.describe('OpenBuilder Full Suite - Advanced Features', () => {
  4   |
  5   |   // 1. Bilingual Navigation & UI Integrity
  6   |   test('Bilingual Switcher & Labels', async ({ page }) => {
  7   |     await page.goto('/index.php?lang=en');
  8   |     await expect(page.locator('h2').first()).toContainText('Project Dashboard');
  9   |
  10  |     await page.goto('/index.php?lang=es');
  11  |     await expect(page.locator('h2').first()).toContainText('Panel de Control');
  12  |   });
  13  |
  14  |   // 2. Interactive Budget Simulator
  15  |   test('Budget Scenario Simulator', async ({ page }) => {
  16  |     await page.goto('/index.php?page=budget&lang=en');
  17  |     const slider = page.locator('#variance-slider');
  18  |     await expect(slider).toBeVisible();
  19  |
  20  |     // Check initial state
  21  |     const impactLabel = page.locator('#projected-impact');
  22  |
  23  |     const totalSpent = await page.evaluate(() => {
  24  |         const text = document.body.innerHTML;
  25  |         const match = text.match(/const totalSpent = (\d+);/);
  26  |         return match ? parseInt(match[1]) : 0;
  27  |     });
  28  |
  29  |     if (totalSpent === 0) {
  30  |       // Just assert it exists and is visible to not fail if there's no data
  31  |       await expect(impactLabel).toBeVisible();
  32  |     } else {
  33  |       await expect(impactLabel).toContainText('$0');
  34  |       // Evaluate via DOM to trigger event listener
  35  |       await page.evaluate(() => {
  36  |           const slider = document.getElementById('variance-slider');
  37  |           slider.value = 25;
  38  |           slider.dispatchEvent(new Event('input', { bubbles: true }));
  39  |       });
  40  |       // Check if the value has been updated to NOT be exactly '$0'
  41  |       await expect(impactLabel).not.toHaveText('$0', { timeout: 1000 });
  42  |     }
  43  |   });
  44  |
  45  |   // 3. RFI Map & Pins
  46  |   test('RFI Spatial Mapping', async ({ page }) => {
  47  |     await page.goto('/index.php?page=rfi_map&lang=en');
  48  |     await expect(page.locator('[data-tooltip*="RFI #001"]')).toBeVisible();
  49  |     await page.goto('/index.php?page=view_rfi&id=1&lang=en');
  50  |     await expect(page.url()).toContain('page=view_rfi');
  51  |   });
  52  |
  53  |   // 4. Admin & Settings
  54  |   test('Project Settings Persistence', async ({ page }) => {
  55  |     await page.goto('/index.php?page=project_settings&lang=en');
  56  |     await page.fill('input[value="OpenBuilder HQ"]', 'Updated Project Name');
  57  |     await page.click('button:has-text("Save Changes")');
  58  |     await expect(page.locator('.toast')).toContainText('Settings saved');
  59  |   });
  60  |
  61  |   test('User Management & Roles', async ({ page }) => {
> 62  |     await page.goto('/index.php?page=users&lang=en');
      |                ^ Error: page.goto: Protocol error (Page.navigate): Cannot navigate to invalid URL
  63  |     await expect(page.locator('text=Admin').first()).toBeVisible();
  64  |     await expect(page.locator('td', { hasText: 'Manager' }).first()).toBeVisible();
  65  |   });
  66  |
  67  |   // 5. Security (MFA Simulation)
  68  |   test('MFA Verification Flow', async ({ page }) => {
  69  |     await page.goto('/index.php?page=mfa&lang=en');
  70  |     await expect(page.locator('h2')).toContainText('Security Verification');
  71  |     const inputs = page.locator('input[maxlength="1"]');
  72  |     await expect(inputs).toHaveCount(6);
  73  |
  74  |     await page.click('button:has-text("Verify")');
  75  |     await expect(page.url()).toContain('page=dashboard');
  76  |   });
  77  |
  78  |   // 6. Mobile Usability
  79  |   test('Mobile Bottom Navigation', async ({ page }) => {
  80  |     await page.setViewportSize({ width: 375, height: 667 }); // iPhone SE size
  81  |     await page.goto('/index.php?lang=en');
  82  |     const bottomNav = page.locator('.fixed.bottom-0.lg\\:hidden');
  83  |     await expect(bottomNav).toBeVisible();
  84  |     await expect(bottomNav.locator('text=Home')).toBeVisible();
  85  |   });
  86  |
  87  |   // 7. AI Elements (Visibility)
  88  |   test('AI Risk Heatmap & Predictions', async ({ page }) => {
  89  |     await page.goto('/index.php?lang=en');
  90  |     await expect(page.locator('h3:has-text("AI Risk Heatmap")')).toBeVisible();
  91  |     await expect(page.locator('h3:has-text("AI Schedule Prediction")')).toBeVisible();
  92  |   });
  93  |
  94  |   // 8. Audit Logs
  95  |   test('Administrative Audit Trail', async ({ page }) => {
  96  |     await page.goto('/index.php?page=audit_logs&lang=en');
  97  |     await expect(page.locator('table')).toBeVisible();
  98  |     await expect(page.locator('td', { hasText: 'Admin' }).first()).toBeVisible();
  99  |   });
  100 |
  101 | });
  102 |
```