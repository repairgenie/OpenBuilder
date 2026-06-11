const { defineConfig, devices } = require('@playwright/test');
const path = require('path');

const BASE_URL = process.env.TEST_BASE_URL || 'http://localhost:8080';

const config = defineConfig({
  testDir: './tests',
  baseURL: BASE_URL,
  fullyParallel: false,
  retries: 2,
  repeatEach: parseInt(process.env.REPEAT_EACH || '1'),
  workers: 1,
  timeout: 60000,
  reporter: [
    ['list'],
    ['html', { open: 'never', outputFolder: 'playwright-report' }],
  ],
  use: {
    baseURL: BASE_URL,
  },
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

// Re-export BASE_URL so tests can `require('../playwright.config').BASE_URL`
config.BASE_URL = BASE_URL;
module.exports = config;
module.exports.BASE_URL = BASE_URL;
