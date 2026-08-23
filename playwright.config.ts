import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.WP_URL || 'http://localhost:8080';

export default defineConfig({
  testDir: './tests/e2e',
  outputDir: './artifacts/playwright',
  reporter: [['html', { outputFolder: 'artifacts/playwright-report', open: 'never' }], ['list']],
  use: { baseURL, trace: 'retain-on-failure' },
  projects: [
    { name: 'mobile-320', use: { ...devices['Desktop Chrome'], viewport: { width: 320, height: 800 } } },
    { name: 'tablet-768', use: { ...devices['Desktop Chrome'], viewport: { width: 768, height: 900 } } },
    { name: 'desktop-1024', use: { ...devices['Desktop Chrome'], viewport: { width: 1024, height: 900 } } },
    { name: 'wide-1440', use: { ...devices['Desktop Chrome'], viewport: { width: 1440, height: 1000 } } }
  ]
});

