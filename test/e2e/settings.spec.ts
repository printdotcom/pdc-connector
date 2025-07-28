import { test, expect } from '@playwright/test';

test.describe('Settings Page', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/wp-admin/admin.php?page=pdc-connector');
  });

  test('when settings page is loaded for the first time, environment is staging', async ({ page }) => {
    // Environment is on test
    await expect(page.getByTestId('pdc-environment')).toHaveValue('stg');

    // Link is going to app.stg.print.com/account
    await expect(page.getByTestId('pdc-environment-link')).toHaveAttribute('href', 'https://app.stg.print.com/account');
  });

  test('user can enter a valid API key and save it', async ({ page }) => {
    // enter key 'test_key_12345'
    await page.getByTestId('pdc-apikey').fill('test_key_12345');

    // save it
    await page.getByRole('button', { name: 'Save Settings' }).click();

    // assert that it is still there
    await expect(page.getByTestId('pdc-apikey')).toHaveValue('test_key_12345');
  });

  test('show notification when environment or key has changed but not saved when verifying', async ({ page }) => {
    // enter just any key
    await page.getByTestId('pdc-apikey').fill('unsaved_key');

    // expect a dialog
    page.on('dialog', async (dialog) => {
      await expect(dialog.message()).toBe('Please save the settings before verifying the API key');
      await dialog.accept();
    });

    // attempt to verify it
    await page.getByTestId('pdc-verify-key').click();
  });

  test('show error when api key is invalid', async ({ page }) => {
    // mock api request to print.com with a 401
    await page.route('https://api.stg.print.com/products', async (route) => {
      route.fulfill({
        status: 401,
        contentType: 'application/json',
        body: JSON.stringify({ error: 'Unauthorized' }),
      });
    });

    // set incorrect key
    await page.getByTestId('pdc-apikey').fill('invalid_key');

    // save it
    await page.getByRole('button', { name: 'Save Settings' }).click();

    // verify it
    await page.getByTestId('pdc-verify-key').click();

    // assert
    await expect(page.getByText('API Key is not valid. Check your environment and API Key')).toBeVisible();
  });

  test('when environment is set to live, show link to production environment', async ({ page }) => {
    // mock api request to print.com with a 200
    await page.route('https://api.print.com/products', async (route) => {
      route.fulfill({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify([]),
      });
    });

    // select prod
    await page.getByTestId('pdc-environment').selectOption('prod');
    await page.getByTestId('pdc-apikey').fill('test_key_12345');

    // save
    await page.getByRole('button', { name: 'Save Settings' }).click();

    // assert
    await expect(page.getByTestId('pdc-environment-link')).toHaveAttribute('href', 'https://app.print.com/account');

    // verify to see if verification request goes to api.print.com and not stg
    await page.getByTestId('pdc-verify-key').click();

    // cleanup
    await page.getByTestId('pdc-environment').selectOption('stg');
    await page.getByRole('button', { name: 'Save Settings' }).click();
  });
});
