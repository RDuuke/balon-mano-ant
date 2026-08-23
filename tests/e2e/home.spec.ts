import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test('inicio responde y no presenta violaciones axe automaticas', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/LABM/i);
  await expect(page.locator('main h1')).toHaveText(/Liga Antioque/i);
  const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21aa', 'wcag22aa']).analyze();
  expect(results.violations).toEqual([]);
});
