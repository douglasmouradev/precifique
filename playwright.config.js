import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    timeout: 30_000,
    retries: 0,
    use: {
        baseURL: process.env.E2E_BASE_URL ?? 'http://127.0.0.1:8000',
        trace: 'on-first-retry',
    },
    webServer: process.env.E2E_BASE_URL
        ? undefined
        : {
              command: 'php artisan serve --no-reload',
              url: 'http://127.0.0.1:8000',
              reuseExistingServer: true,
              timeout: 120_000,
          },
});
