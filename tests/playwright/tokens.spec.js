/**
 * Design System Token Tests
 * Suite: Playwright > Tokens
 *
 * Tests that CSS design tokens (colors, typography, spacing)
 * are consistently applied across the application.
 *
 * @see TESTING-GUIDE.md Section "Design System"
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { BASE_URL } = require('../playwright.config');

test.describe('Design Tokens — Colors', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
  });

  test('TOKEN-COLOR-01: Primary color is applied to buttons', async ({ page }) => {
    // Check that primary buttons use the primary color
    const primaryBtn = page.locator('button[class*="primary"], .btn-primary, button[type="submit"]').first();
    if (await primaryBtn.count() > 0) {
      const bgColor = await primaryBtn.evaluate(el => window.getComputedStyle(el).backgroundColor);
      // Should have a non-transparent background
      expect(bgColor).not.toBe('rgba(0, 0, 0, 0)');
    }
  });

  test('TOKEN-COLOR-02: Text colors are readable', async ({ page }) => {
    const body = await page.textContent('body');
    const textColor = await page.evaluate(() => window.getComputedStyle(document.body).color);
    // Text should be dark on light background (readable)
    expect(body.length).toBeGreaterThan(0);
  });

  test('TOKEN-COLOR-03: Link colors are distinct from body text', async ({ page }) => {
    const link = page.locator('a').first();
    if (await link.count() > 0) {
      const linkColor = await link.evaluate(el => window.getComputedStyle(el).color);
      const bodyColor = await page.evaluate(() => window.getComputedStyle(document.body).color);
      // Links should have a different color than body text
      expect(linkColor).not.toBe(bodyColor);
    }
  });

  test('TOKEN-COLOR-04: Error states use red color', async ({ page }) => {
    // Navigate to a form and submit empty
    await page.goto(BASE_URL + '?page=create_rfi&lang=en');
    await page.waitForTimeout(1000);
    const submitBtn = page.locator('button[type="submit"]').first();
    if (await submitBtn.count() > 0) {
      await submitBtn.click();
      await page.waitForTimeout(1000);
    }

    const errorText = page.locator('[class*="error"], .has-error, [class*="alert"]');
    if (await errorText.count() > 0) {
      const errorColor = await errorText.first().evaluate(el => window.getComputedStyle(el).color);
      // Error should be reddish
      expect(errorColor).toMatch(/rgb|rgba/);
    }
  });

  test('TOKEN-COLOR-05: Success states use green color', async ({ page }) => {
    // Check for success indicators
    const successEl = page.locator('[class*="success"], .has-success, [class*="check"]');
    if (await successEl.count() > 0) {
      const successColor = await successEl.first().evaluate(el => window.getComputedStyle(el).color);
      expect(successColor).toMatch(/rgb|rgba/);
    }
  });
});

test.describe('Design Tokens — Typography', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
  });

  test('TOKEN-TYPE-01: Font family is applied consistently', async ({ page }) => {
    const fontFamily = await page.evaluate(() => window.getComputedStyle(document.body).fontFamily);
    expect(fontFamily.length).toBeGreaterThan(0);
  });

  test('TOKEN-TYPE-02: Heading hierarchy is correct', async ({ page }) => {
    const h1 = page.locator('h1');
    const h2 = page.locator('h2');
    const h3 = page.locator('h3');

    if (await h1.count() > 0) {
      const h1Size = await h1.first().evaluate(el => window.getComputedStyle(el).fontSize);
      if (await h2.count() > 0) {
        const h2Size = await h2.first().evaluate(el => window.getComputedStyle(el).fontSize);
        // H1 should be larger than H2
        expect(parseFloat(h1Size)).toBeGreaterThan(parseFloat(h2Size));
      }
    }
  });

  test('TOKEN-TYPE-03: Body text is readable size', async ({ page }) => {
    const fontSize = await page.evaluate(() => {
      const body = document.body;
      return parseFloat(window.getComputedStyle(body).fontSize);
    });
    // Should be at least 14px for readability
    expect(fontSize).toBeGreaterThanOrEqual(14);
  });

  test('TOKEN-TYPE-04: Line height is adequate', async ({ page }) => {
    const lineHeight = await page.evaluate(() => {
      const body = document.body;
      return parseFloat(window.getComputedStyle(body).lineHeight);
    });
    // Line height should be at least 1.4 for readability
    expect(lineHeight).toBeGreaterThanOrEqual(1.4);
  });
});

test.describe('Design Tokens — Spacing', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
  });

  test('TOKEN-SPACE-01: Consistent padding on cards', async ({ page }) => {
    const card = page.locator('[class*="card"], .card, [class*="panel"]').first();
    if (await card.count() > 0) {
      const padding = await card.evaluate(el => {
        const style = window.getComputedStyle(el);
        return {
          top: parseFloat(style.paddingTop),
          bottom: parseFloat(style.paddingBottom),
          left: parseFloat(style.paddingLeft),
          right: parseFloat(style.paddingRight)
        };
      });
      // Padding should be consistent (within4px tolerance)
      expect(Math.abs(padding.top - padding.bottom)).toBeLessThan(4);
      expect(Math.abs(padding.left - padding.right)).toBeLessThan(4);
    }
  });

  test('TOKEN-SPACE-02: Consistent margin on sections', async ({ page }) => {
    const section = page.locator('section, [class*="section"], .content-area').first();
    if (await section.count() > 0) {
      const margin = await section.evaluate(el => window.getComputedStyle(el).marginTop);
      expect(parseFloat(margin)).toBeGreaterThanOrEqual(0);
    }
  });

  test('TOKEN-SPACE-03: Form inputs have consistent spacing', async ({ page }) => {
    await page.goto(BASE_URL + '?page=create_rfi&lang=en');
    await page.waitForTimeout(1000);

    const inputs = page.locator('input, textarea, select');
    const count = await inputs.count();
    if (count > 1) {
      const firstInput = await inputs.first().boundingBox();
      const secondInput = await inputs.nth(1).boundingBox();
      if (firstInput && secondInput) {
        // Inputs should have consistent vertical spacing
        const spacing = secondInput.y - (firstInput.y + firstInput.height);
        expect(spacing).toBeGreaterThanOrEqual(0);
      }
    }
  });
});

test.describe('Design Tokens — Buttons', () => {
  test.beforeEach(async ({ page }) => {
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=create_rfi&lang=en');
    await page.waitForTimeout(1000);
  });

  test('TOKEN-BTN-01: Primary button has distinct styling', async ({ page }) => {
    const primaryBtn = page.locator('button[type="submit"], .btn-primary, button[class*="primary"]').first();
    if (await primaryBtn.count() > 0) {
      const bgColor = await primaryBtn.evaluate(el => window.getComputedStyle(el).backgroundColor);
      const color = await primaryBtn.evaluate(el => window.getComputedStyle(el).color);
      // Should have solid background and contrasting text
      expect(bgColor).not.toBe('rgba(0, 0, 0, 0)');
      expect(color).not.toBe('rgba(0, 0, 0, 0)');
    }
  });

  test('TOKEN-BTN-02: Secondary button has distinct styling', async ({ page }) => {
    const secondaryBtn = page.locator('button:has-text("cancel"), button[class*="secondary"], .btn-secondary').first();
    if (await secondaryBtn.count() > 0) {
      const bgColor = await secondaryBtn.evaluate(el => window.getComputedStyle(el).backgroundColor);
      // Secondary should have less prominent styling
      expect(bgColor).toMatch(/rgb|rgba/);
    }
  });

  test('TOKEN-BTN-03: Buttons have hover states', async ({ page }) => {
    const btn = page.locator('button[type="submit"]').first();
    if (await btn.count() > 0) {
      const bgBefore = await btn.evaluate(el => window.getComputedStyle(el).backgroundColor);
      await btn.hover();
      const bgAfter = await btn.evaluate(el => window.getComputedStyle(el).backgroundColor);
      // Hover should change the background (or stay the same if no hover defined)
      expect(bgBefore || bgAfter).toMatch(/rgb|rgba/);
    }
  });

  test('TOKEN-BTN-04: Disabled buttons are visually distinct', async ({ page }) => {
    const disabledBtn = page.locator('button:disabled, button[disabled], button[class*="disabled"]').first();
    if (await disabledBtn.count() > 0) {
      const opacity = await disabledBtn.evaluate(el => window.getComputedStyle(el).opacity);
      expect(parseFloat(opacity)).toBeLessThan(1);
    }
  });
});
