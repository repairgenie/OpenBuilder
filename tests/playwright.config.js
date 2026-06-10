const { defineConfig, devices } = require('@playwright/test');
const path = require('path');

module.exports = defineConfig({
  testDir: './tests',
  baseURL: process.env.TEST_BASE_URL || 'http://localhost:8080',
  fullyParallel: false,
  retries: 2,
  repeatEach: parseInt(process.env.REPEAT_EACH || '1'),
  workers: 1,
  timeout: 60000,
  reporter: [
    ['list'],
    ['html', { open: 'never', outputFolder: 'playwright-report' }],
  ],
  projects: [
    // ── Auth Setup ──────────────────────────────────────────────────────────
    {
      name: 'chromium-setup',
      use: { ...devices['Desktop Chrome'] },
      testMatch: 'tests/auth/setup.spec.js',
    },
    // ── Authentication Tests ───────────────────────────────────────────────
    {
      name: 'chromium-auth',
      use: { ...devices['Desktop Chrome'] },
      testMatch: 'tests/auth/*.spec.js',
      dependencies: ['chromium-setup'],
    },
    // ── Bilingual Tests ────────────────────────────────────────────────────
    {
      name: 'chromium-bilingual',
      use: { ...devices['Desktop Chrome'] },
      testMatch: 'tests/bilingual/*.spec.js',
      dependencies: ['chromium-setup'],
    },
    // ── Workflow Tests ─────────────────────────────────────────────────────
    {
      name: 'chromium-workflows',
      use: { ...devices['Desktop Chrome'] },
      testMatch: 'tests/flows/*.spec.js',
      dependencies: ['chromium-setup'],
    },
    // ── Page Element Tests ─────────────────────────────────────────────────
    {
      name: 'chromium-pages',
      use: { ...devices['Desktop Chrome'] },
      testMatch: 'tests/pages/*.spec.js',
      dependencies: ['chromium-setup'],
    },
    // ── Security Tests ────────────────────────────────────────────────────
    {
      name: 'chromium-security',
      use: { ...devices['Desktop Chrome'] },
      testMatch: 'tests/security/*.spec.js',
      dependencies: ['chromium-setup'],
    },
    // ── Edge Case Tests ───────────────────────────────────────────────────
    {
      name: 'chromium-edge',
      use: { ...devices['Desktop Chrome'] },
      testMatch: 'tests/edge-cases/*.spec.js',
      dependencies: ['chromium-setup'],
    },
  ],
  webServer: [],
});
