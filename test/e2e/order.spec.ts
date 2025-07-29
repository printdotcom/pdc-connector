import { test, expect } from '@playwright/test';
import { configurePoster, orderPoster, setSettings } from './utils';

test.describe('Order', () => {
  test('has print.com meta table visible', async ({ page }) => {
    await setSettings(page, {
      apikey: 'test_key_12345',
      env: 'stg',
      usePresetCopies: true,
    });

    await configurePoster(page);

    await orderPoster(page);

    await page.goto('http://localhost:8060/wp-admin/edit.php?post_type=shop_order');

    // view latest order
    await page.locator('table.wp-list-table tbody tr:first-child a.order-view').click();

    await expect(page.getByRole('heading', { name: 'Print.com' })).toBeVisible();
  });
});
