import { test, expect } from '@playwright/test';
import { configurePoster, orderPoster, setSettings } from './utils';

test.describe('Order', () => {
  test('will purchase the preset copies amount when use_preset_copies is true', async ({ page }) => {
    await setSettings(page, {
      apikey: 'test_key_12345',
      env: 'stg',
      usePresetCopies: true,
    });

    await configurePoster(page);

    await orderPoster(page);

    await page.goto('/wp-admin/edit.php?post_type=shop_order');

    // view latest order
    await page.locator('table.wp-list-table tbody tr:first-child a.order-view').click();


    // purchase it
    await page.getByTestId('pdc-purchase-orderitem').click();

    // We have configured a preset with 300 copies (see preset.123poster.json), so should be 300 copies.
    await expect(page.getByTestId('pdc-ordered-copies')).toHaveText('Copies 300');
  });

  test('will purchase the ordered quantity when use_preset_copies is false', async ({ page }) => {
    await setSettings(page, {
      apikey: 'test_key_12345',
      env: 'stg',
      usePresetCopies: false,
    });

    await configurePoster(page);

    await orderPoster(page);

    await page.goto('/wp-admin/edit.php?post_type=shop_order');

    // view latest order
    await page.locator('table.wp-list-table tbody tr:first-child a.order-view').click();

    // purchase it
    await page.getByTestId('pdc-purchase-orderitem').click();

    // quanity = 1 so makes 1 copy
    await expect(page.getByTestId('pdc-ordered-copies')).toHaveText('Copies 1');
  });
});
