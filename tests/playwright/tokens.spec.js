const { test, expect } = require('@playwright/test');

test('Design System Token Test', async ({ page }) => {
  await page.goto('http://localhost:8000');
  
  await page.waitForLoadState('networkidle');
  // Verify that a CSS variable is accessible
  const primaryColor = await page.evaluate(() => {
    return getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim();
  });
  
  expect(primaryColor).toBe('hsl(231, 74%, 56%)');
  
  // Verify body background color
  const bodyBg = await page.evaluate(() => {
    return getComputedStyle(document.body).backgroundColor;
  });
  // rgb(241, 245, 249) is the hex #F1F5F9 / hsl(210, 40%, 96%)
  expect(bodyBg).toBe('rgb(248, 250, 252)');
});
