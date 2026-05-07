const { test, expect, devices } = require('@playwright/test');

test('Mobile Menu Verification', async ({ page }) => {
  // Use iPhone 12 viewport
  await page.setViewportSize(devices['iPhone 12'].viewport);
  await page.goto('http://localhost:8000');
  
  const sidebar = page.locator('aside');
  const toggle = page.locator('#mobile-toggle');
  
  // Verify sidebar is hidden initially on mobile
  await expect(sidebar).toHaveClass(/-translate-x-full/);
  
  // Toggle menu
  await toggle.click();
  await expect(sidebar).not.toHaveClass(/-translate-x-full/);
  
  // Verify overlay
  const overlay = page.locator('#sidebar-overlay');
  await expect(overlay).toBeVisible();
  
  // Close via overlay
  await overlay.click();
  await expect(sidebar).toHaveClass(/-translate-x-full/);
});
