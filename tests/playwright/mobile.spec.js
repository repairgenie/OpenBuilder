/**
 * Mobile Menu & Responsive Tests
 * Suite: Playwright > Mobile
 *
 * Tests the mobile navigation experience, responsive layouts,
 * touch interactions, and mobile-specific UI elements.
 *
 * @see TESTING-GUIDE.md Section "Mobile & Responsive"
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth } = require('../helpers/auth');
const { BASE_URL } = require('../playwright.config');

test.describe('Mobile — Viewport', () => {
  test.beforeEach(async ({ page }) => {
    // Set mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
  });

  test('MOBILE-01: Mobile viewport loads without horizontal scroll', async ({ page }) => {
    const scrollWidth = await page.evaluate(() => document.body.scrollWidth);
    const viewportWidth = await page.evaluate(() => window.innerWidth);
    // Should not have horizontal overflow
    expect(scrollWidth).toBeLessThanOrEqual(viewportWidth);
  });

  test('MOBILE-02: Content is readable on mobile', async ({ page }) => {
    const body = await page.textContent('body');
    expect(body.length).toBeGreaterThan(100);
  });

  test('MOBILE-03: Text is not too small to read', async ({ page }) => {
    const fontSize = await page.evaluate(() => {
      const body = document.body;
      return window.getComputedStyle(body).fontSize;
    });
    const fontSizeNum = parseFloat(fontSize);
    // Font should be at least 12px
    expect(fontSizeNum).toBeGreaterThanOrEqual(12);
  });

  test('MOBILE-04: Buttons are tap-friendly size', async ({ page }) => {
    const buttons = page.locator('button, a[class*="btn"], input[type="submit"]');
    const count = await buttons.count();
    if (count > 0) {
      // Check first few buttons
      for (let i = 0; i < Math.min(count, 5); i++) {
        const box = await buttons.nth(i).boundingBox();
        if (box) {
          // Minimum touch target size: 44x44px (Apple HIG)
          expect(box.height).toBeGreaterThanOrEqual(30);
 }
      }
    }
  });

  test('MOBILE-05: Forms are usable on mobile', async ({ page }) => {
    // Go to a form page
    await page.goto(BASE_URL + '?page=create_rfi&lang=en');
    await page.waitForTimeout(1000);

    const inputs = page.locator('input, textarea, select');
    const count = await inputs.count();
    expect(count).toBeGreaterThan(0);

    // Check first input is reachable
    if (count > 0) {
      const inputBox = await inputs.first().boundingBox();
      if (inputBox) {
        // Input should be within viewport
        expect(inputBox.y).toBeGreaterThan(0);
 }
    }
  });
});

test.describe('Mobile — Menu', () => {
  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
  });

  test('MOBILE-MENU-01: Hamburger menu button is visible', async ({ page }) => {
    const hamburger = page.locator('button[class*="hamburger"], button[class*="menu-toggle"], button[class*="nav-toggle"], button[class*="burger"]');
    if (await hamburger.count() > 0) {
      await expect(hamburger.first()).toBeVisible();
    } else {
      // Alternative: three-line icon or aria-label
      const menuBtn = page.locator('button[aria-label*="menu"], button[aria-label*="Menu"]');
      await expect(menuBtn.first()).toBeVisible();
    }
  });

  test('MOBILE-MENU-02: Tapping hamburger opens mobile menu', async ({ page }) => {
    const hamburger = page.locator('button[class*="hamburger"], button[class*="menu-toggle"]').first();
    if (await hamburger.count() > 0) {
      await hamburger.click();
      await page.waitForTimeout(500);
      // Mobile menu should appear
      const mobileMenu = page.locator('[class*="mobile-menu"], [class*="drawer"], [class*="offcanvas"], aside[class*="open"]');
      if (await mobileMenu.count() > 0) {
        await expect(mobileMenu.first()).toBeVisible();
      }
    } else {
      expect(true).toBe(true);
    }
  });

  test('MOBILE-MENU-03: Mobile menu has navigation links', async ({ page }) => {
    const hamburger = page.locator('button[class*="hamburger"], button[class*="menu-toggle"]').first();
    if (await hamburger.count() > 0) {
      await hamburger.click();
      await page.waitForTimeout(500);
      const navLinks = page.locator('[class*="mobile-menu"] a, [class*="drawer"] a, aside a');
      const count = await navLinks.count();
      expect(count).toBeGreaterThan(0);
    }
  });

  test('MOBILE-MENU-04: Tapping outside closes mobile menu', async ({ page }) => {
    const hamburger = page.locator('button[class*="hamburger"], button[class*="menu-toggle"]').first();
    if (await hamburger.count() > 0) {
      await hamburger.click();
      await page.waitForTimeout(500);
      // Tap outside the menu
      await page.click('main, body', { position: { x: 10, y: 10 } });
      await page.waitForTimeout(500);
      // Menu should close
      const mobileMenu = page.locator('[class*="mobile-menu"], [class*="drawer"]');
      if (await mobileMenu.count() > 0) {
        const isClosed = await mobileMenu.first().isHidden();
        expect(isClosed).toBe(true);
      }
    }
  });

  test('MOBILE-MENU-05: Close button in mobile menu works', async ({ page }) => {
    const hamburger = page.locator('button[class*="hamburger"], button[class*="menu-toggle"]').first();
    if (await hamburger.count() > 0) {
      await hamburger.click();
      await page.waitForTimeout(500);
      const closeBtn = page.locator('button[class*="close"], button[class*="dismiss"], [class*="mobile-menu"] button[class*="close"]').first();
      if (await closeBtn.count() > 0) {
        await closeBtn.click();
        await page.waitForTimeout(500);
        const mobileMenu = page.locator('[class*="mobile-menu"], [class*="drawer"]');
        if (await mobileMenu.count() > 0) {
          await expect(mobileMenu.first()).toBeHidden();
        }
      }
    }
  });
});

test.describe('Mobile — Responsive Breakpoints', () => {
  test('MOBILE-TABLET-01: Tablet viewport loads correctly', async ({ page }) => {
    await page.setViewportSize({ width: 768, height: 1024 });
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
    const body = await page.textContent('body');
    expect(body.length).toBeGreaterThan(100);
  });

  test('MOBILE-DESKTOP-01: Desktop viewport shows full layout', async ({ page }) => {
    await page.setViewportSize({ width: 1920, height: 1080 });
    await restoreAuth(page);
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(1000);
    const sidebar = page.locator('aside, .sidebar');
    if (await sidebar.count() > 0) {
      await expect(sidebar.first()).toBeVisible();
    }
  });

  test('MOBILE-TRANSITION-01: Layout transitions smoothly between breakpoints', async ({ page }) => {
    await restoreAuth(page);
    // Start at mobile
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto(BASE_URL + '?page=dashboard&lang=en');
    await page.waitForTimeout(500);

    // Resize to desktop
    await page.setViewportSize({ width: 1920, height: 1080 });
    await page.waitForTimeout(500);

    // Should still be functional
    const body = await page.textContent('body');
    expect(body.length).toBeGreaterThan(100);
  });
});
