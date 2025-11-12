import path from 'path';
import { test, expect } from '@playwright/test';
import { configureSimpleProduct } from './utils';

test.describe('product', () => {
  test('can configure a preset for a simple product', async ({ page }) => {
    await configureSimpleProduct(page, '14');
    await page.getByRole('link', { name: 'Print.com' }).click();
    await expect(page.getByTestId('pdc-preset-id')).toHaveValue('flyers_a5');
  });

  test('can configure a PDF for a product', async ({ page }) => {
    await page.goto('/wp-admin/post.php?post=14&action=edit');
    await page.getByRole('link', { name: 'Print.com' }).click();

    // select product
    await page.getByTestId('pdc-product-sku').selectOption('flyers');

    await page.getByRole('link', { name: 'Choose file' }).click();
    await page.getByRole('tab', { name: 'Upload files' }).click();

    const fileChooserPromise = page.waitForEvent('filechooser');
    await page.getByRole('button', { name: 'Select Files' }).click();
    const fileChooser = await fileChooserPromise;
    await fileChooser.setFiles(path.join(__dirname, `/fixtures/pdc_flyera5.pdf`));

    await page.getByRole('button', { name: 'Select File', exact: true }).click();

    await page.getByRole('button', { name: 'Update' }).click();

    await page.getByRole('link', { name: 'Print.com' }).click();

    const input = page.getByTestId('pdc-file-upload');
    await expect(input).toHaveValue(/pdc_flyera5/);
  });

  test('can configure a preset and file for a variable product', async ({ page }) => {
    await page.goto('/wp-admin/post.php?post=15&action=edit');

    // go to pdc tab
    await page.getByRole('link', { name: 'Print.com' }).click();

    // select product
    await page.getByTestId('pdc-product-sku').selectOption('posters');

    // save
    await page.getByRole('button', { name: 'Update' }).click();
    
    // go to variations tab and wait for panel to appear
    await page.locator('a[href="#variable_product_options"]').click();
    await page.locator('#variable_product_options').waitFor({ state: 'visible' });

    await expect(page.getByRole('heading', { name: /A2/i })).toBeVisible();

    // open A2
    await page.getByRole('heading', { name: /A2/i }).click();
    await page.getByTestId('variation_preset_1').waitFor({ state: 'visible' });

    // select preset
    await page.getByTestId('variation_preset_1').selectOption('posters_a2');

    // save variation
    await page.getByRole('button', { name: 'Save changes' }).click();
  });
});
