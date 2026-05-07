const { test, expect } = require('@playwright/test');

test('RFI UI Verification', async ({ page }) => {
  await page.goto('http://localhost:8000?page=rfis&lang=en');
  
  // Verify Table Headers
  await expect(page.locator('th')).toContainText(['Ref #', 'Subject', 'Priority', 'Status', 'Action']);
  
  // Verify Status Badge (assuming there is at least one Open RFI)
  const openBadge = page.locator('.bg-warning.text-white');
  if (await openBadge.count() > 0) {
    await expect(openBadge.first()).toContainText('Open');
  }
  
  // Switch to Spanish
  await page.goto('http://localhost:8000?page=rfis&lang=es');
  await expect(page.locator('th')).toContainText(['Ref #', 'Asunto', 'Prioridad', 'Estado', 'Acción']);
});
