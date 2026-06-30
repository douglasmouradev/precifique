import { test, expect } from '@playwright/test';

test('landing page loads', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveTitle(/Precifique/i);
    await expect(page.locator('#landing-header')).toBeVisible();
});

test('tenant login page loads', async ({ page }) => {
    await page.goto('/entrar');
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
});
