import path from 'path';

export async function orderPoster(page) {
  await page.goto('/?product=custom-poster-a3');
  await page.getByRole('button', { name: 'Add to cart', exact: true }).click();
  await page.getByRole('link', { name: 'View cart' }).click();
  await page.getByRole('link', { name: 'Proceed to checkout' }).click();
  await page.locator('#billing_first_name').fill('Test');
  await page.locator('#billing_last_name').fill('User');
  await page.getByRole('textbox', { name: 'Company name (optional)' }).fill('Print.com');
  await page.getByRole('textbox', { name: 'Street address' }).fill('Teugseweg 18a');
  await page.getByRole('textbox', { name: 'Town / City' }).fill('Deventer');
  await page.getByRole('textbox', { name: 'ZIP Code' }).fill('63104');
  await page.getByRole('textbox', { name: 'Phone' }).fill('0612312312');
  await page.getByRole('button', { name: 'Place order' }).click();
  await page.waitForResponse(/\/?wc-ajax=checkout/);
}

interface Settings {
  apikey: string;
  env: 'prod' | 'stg';
  usePresetCopies: boolean;
}
export async function setSettings(page, settings: Settings) {
  await page.goto('/wp-admin/admin.php?page=pdc-connector');
  await page.getByTestId('pdc-apikey').fill(settings.apikey);
  await page.getByTestId('pdc-environment').selectOption('stg');

  if (settings.usePresetCopies) {
    await page.getByTestId('pdc-use_preset_copies').check();
  } else {
    await page.getByTestId('pdc-use_preset_copies').uncheck();
  }

  await page.getByRole('button', { name: 'Save Settings' }).click();
}

export async function configurePoster(page) {
  await page.goto('/wp-admin/post.php?post=16&action=edit');
  await page.getByRole('link', { name: 'Print.com' }).click();

  // sku = poster
  await page.locator('#js-pdc-ac-product-list #pdc-products-label').fill('post');
  await page.waitForResponse(/\/wp-admin\/admin-ajax.php/);
  await page.getByRole('option', { name: 'Posters posters' }).click();

  // preset = A3 Posters
  await page.locator('#pdc-presets-label').click();
  await page.waitForResponse(/\/wp-admin\/admin-ajax.php/);
  await page.getByRole('option', { name: 'A3 Posters' }).click();
  await page;

  // pdf file = fixture
  await page.getByRole('link', { name: 'Choose file' }).click();
  await page.getByRole('tab', { name: 'Upload files' }).click();
  const fileChooserPromise = page.waitForEvent('filechooser');
  await page.getByRole('button', { name: 'Select Files' }).click();
  const fileChooser = await fileChooserPromise;
  await fileChooser.setFiles(path.join(__dirname, `/fixtures/pdc_flyera5.pdf`));
  await page.getByRole('button', { name: 'Select File', exact: true }).click();

  await page.getByRole('button', { name: 'Update' }).click();
}
