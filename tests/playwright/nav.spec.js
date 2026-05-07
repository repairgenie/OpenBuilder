const { test, expect } = require('@playwright/test');

test('Dynamic Navigation and Language Test', async ({ page }) => {
  await page.goto('http://localhost:8000');
  
  // Default is English
  await expect(page.locator('#nav-dashboard')).toContainText('Dashboard');
  
  // Switch to Spanish
  await page.click('text=ES');
  await expect(page).toHaveURL(/lang=es/);
  await expect(page.locator('#nav-dashboard')).toContainText('Panel');
  
  // Check RFI translation
  await expect(page.locator('#nav-rfis')).toContainText('Solicitudes');
});
