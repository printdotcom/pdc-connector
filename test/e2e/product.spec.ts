import path from 'path';
import { test, expect } from '@playwright/test';
import { configurePoster } from './utils';

test.describe('product', () => {
  test('can configure a preset for a poster', async ({ page }) => {
    await configurePoster(page);
    await page.getByRole('link', { name: 'Print.com' }).click();
    await expect(page.locator('#pdc-presets-label')).toHaveValue('A3 Posters');
  });

  test('can configure a PDF for a product', async ({ page }) => {
    await page.goto('http://localhost:8060/wp-admin/post.php?post=16&action=edit');
    await page.getByRole('link', { name: 'Print.com' }).click();

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
});
