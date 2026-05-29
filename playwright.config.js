const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests',
  baseURL: process.env.TEST_BASE_URL || 'http://localhost:9000',
  fullyParallel: false,
  retries: 2,
  repeatEach: parseInt(process.env.REPEAT_EACH || '1'),
  workers: 1,
  timeout: 60000,
  reporter: [['list'], ['html', { open: 'never' }]],
  projects: [
    {
      name: 'chromium-setup',
      use: { ...devices['Desktop Chrome'] },
      testMatch: 'tests/playwright/auth.setup.spec.js',
    },
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
      testMatch: '**/contract_workflow.spec.js',
      dependencies: ['chromium-setup'],
    },
  ],
  webServer: [],
});
