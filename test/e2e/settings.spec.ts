import { test, expect } from '@playwright/test';
import { setSettings } from './utils';

test.describe('Settings Page', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/wp-admin/admin.php?page=pdc-pod');
  });

  test.describe('general settings', () => {
    test('when settings page is loaded for the first time, environment is staging', async ({ page }) => {
      // Environment is on test
      await expect(page.getByTestId('pdc-pod-environment')).toHaveValue('stg');

      // Link is going to app.stg.print.com/account
      await expect(page.getByTestId('pdc-pod-environment-link')).toHaveAttribute('href', 'https://app.stg.print.com/account');
    });

    test('user can enter a valid API key and save it', async ({ page }) => {
      // enter key 'test_key_12345'
      await page.getByTestId('pdc-pod-apikey').fill('test_key_12345');

      // save it
      await page.getByRole('button', { name: 'Save Settings' }).click();

      // assert that it is still there
      await expect(page.getByTestId('pdc-pod-apikey')).toHaveValue('test_key_12345');
    });

    test('show notification when environment or key has changed but not saved when verifying', async ({ page }) => {
      // enter just any key
      await page.getByTestId('pdc-pod-apikey').fill('unsaved_key');

      // expect a dialog
      page.on('dialog', async (dialog) => {
        await expect(dialog.message()).toBe('Please save the settings before verifying the API key');
        await dialog.accept();
      });

      // attempt to verify it
      await page.getByTestId('pdc-pod-verify-key').click();
    });

    test('show error when api key is invalid', async ({ page }) => {
      // set incorrect key
      await page.getByTestId('pdc-pod-apikey').fill('invalid_key');

      // save it
      await page.getByRole('button', { name: 'Save Settings' }).click();

      // verify it
      await page.getByTestId('pdc-pod-verify-key').click();

      // assert
      await expect(page.getByText('API Key is not valid. Check your environment and API Key')).toBeVisible();
    });

    test('when environment is set to live, show link to production environment', async ({ page }) => {
      // select prod
      await page.getByTestId('pdc-pod-environment').selectOption('prod');
      await page.getByTestId('pdc-pod-apikey').fill('test_key_12345');

      // save
      await page.getByRole('button', { name: 'Save Settings' }).click();

      // assert
      await expect(page.getByTestId('pdc-pod-environment-link')).toHaveAttribute('href', 'https://app.print.com/account');

      // verify to see if verification request goes to api.print.com and not stg
      await page.getByTestId('pdc-pod-verify-key').click();

      // assert
      await expect(page.getByText('API Key verified. You are now connected!')).toBeVisible();

      // cleanup
      await page.getByTestId('pdc-pod-environment').selectOption('stg');
      await page.getByRole('button', { name: 'Save Settings' }).click();
    });
  });

  test.describe('product settings', () => {
    test.beforeEach(async ({ page }) => {
      await page.goto('/wp-admin/admin.php?page=pdc-pod&tab=product');
    });

    test.afterEach(async ({ page }) => {
      await setSettings(page, {
        apikey: 'test_key_12345',
        env: 'stg',
        usePresetCopies: true,
      });
    });

    test('user can check the preset copies checkbox and save it', async ({ page }) => {
      await page.getByTestId('pdc-pod-use_preset_copies').check();

      await expect(page.getByTestId('pdc-pod-use_preset_copies')).toBeChecked();

      await page.getByRole('button', { name: 'Save Settings' }).click();

      await page.reload();
      await expect(page.getByTestId('pdc-pod-use_preset_copies')).toBeChecked();
    });

    test('user can uncheck the preset copies checkbox and save it', async ({ page }) => {
      await page.getByTestId('pdc-pod-use_preset_copies').check();
      await page.getByRole('button', { name: 'Save Settings' }).click();
      await expect(page.getByTestId('pdc-pod-use_preset_copies')).toBeChecked();

      await page.getByTestId('pdc-pod-use_preset_copies').uncheck();
      await expect(page.getByTestId('pdc-pod-use_preset_copies')).not.toBeChecked();

      await page.getByRole('button', { name: 'Save Settings' }).click();

      await page.reload();
      await expect(page.getByTestId('pdc-pod-use_preset_copies')).not.toBeChecked();
    });

    test('checkbox state persists across page reloads', async ({ page }) => {
      await page.getByTestId('pdc-pod-use_preset_copies').check();
      await page.getByRole('button', { name: 'Save Settings' }).click();

      await page.goto('/wp-admin/admin.php?page=pdc-pod&tab=product');

      await expect(page.getByTestId('pdc-pod-use_preset_copies')).toBeChecked();
    });

    test('both general and product settings can be saved together', async ({ page }) => {
      await setSettings(page, {
        apikey: 'combined_test_key',
        env: 'stg',
        usePresetCopies: true,
      });

      await page.goto('/wp-admin/admin.php?page=pdc-pod&tab=product');
      await page.getByTestId('pdc-pod-use_preset_copies').check();
      await page.getByRole('button', { name: 'Save Settings' }).click();
      
      await page.goto('/wp-admin/admin.php?page=pdc-pod');
      await expect(page.getByTestId('pdc-pod-apikey')).toHaveValue('combined_test_key');
      await expect(page.getByTestId('pdc-pod-environment')).toHaveValue('stg');
      await page.goto('/wp-admin/admin.php?page=pdc-pod&tab=product');
      await expect(page.getByTestId('pdc-pod-use_preset_copies')).toBeChecked();
      
      await page.goto('/wp-admin/admin.php?page=pdc-pod');
      await expect(page.getByTestId('pdc-pod-environment')).toHaveValue('stg');
      await page.goto('/wp-admin/admin.php?page=pdc-pod&tab=product');
      await expect(page.getByTestId('pdc-pod-use_preset_copies')).toBeChecked();
    });
  });
});
