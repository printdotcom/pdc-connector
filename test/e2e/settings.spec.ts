import { test, expect } from '@playwright/test';

test.describe('Settings Page', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/wp-admin/admin.php?page=pdc-connector');
  });

  test('when settings page is loaded for the first time, environment is staging and API key is empty', async ({ page }) => {
    // API Key is empty
    await expect(page.getByTestId('pdc-apikey')).toBeEmpty();

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

  test('show notification when environment or key has changed but not saved when verifying', async () => {});

  test('show error when api key is invalid', async () => {});

  test('when environment is set to live, show link to production environment', async () => {});
});
