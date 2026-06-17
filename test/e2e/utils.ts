import { expect } from '@playwright/test';
import path from 'path';

interface AddToCartParam {
  slug: string;
  options?: { [key: string]: string };
}
export async function addToCart(page, params: AddToCartParam) {
  await page.goto(`/?product=${encodeURIComponent(params.slug)}`);
  if (params.options) {
    for (const [key, value] of Object.entries(params.options)) {
      await page.locator(`#${key}`).selectOption(value);
    }
  }
  const addToCartButton = page.getByRole('button', { name: 'Add to cart', exact: true });
  await expect(addToCartButton).not.toHaveClass(/disabled/);
  await addToCartButton.click();
}

export async function placeOrder(page) {
  // 12 = checkout in seeder
  await page.goto(`/?page_id=12`);
  await page.locator('#billing_first_name').fill('Test');
  await page.locator('#billing_last_name').fill('User');
  await page.getByRole('textbox', { name: 'Street address' }).fill('Teugseweg 18a');
  await page.getByRole('textbox', { name: 'Town / City' }).fill('Deventer');
  await page.locator('#billing_postcode').fill('63104');
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
  await page.goto('/wp-admin/admin.php?page=pdc-pod');
  await page.getByTestId('pdc-pod-apikey').fill(settings.apikey);
  await page.getByTestId('pdc-pod-environment').selectOption('stg');
  await page.getByRole('button', { name: 'Save Settings' }).click();

  await page.goto('/wp-admin/admin.php?page=pdc-pod&tab=product');
  if (settings.usePresetCopies) {
    await page.getByTestId('pdc-pod-use_preset_copies').check();
  } else {
    await page.getByTestId('pdc-pod-use_preset_copies').uncheck();
  }
  await page.getByRole('button', { name: 'Save Settings' }).click();
}

export async function configureSimpleProduct(page, productID: string, presetID: string = 'flyers_a5') {
  const productURL = `/wp-admin/post.php?post=${productID}&action=edit`;
  await page.goto(productURL);
  await page.getByRole('link', { name: 'Print.com' }).click();

  // select product
  await page.getByTestId('pdc-product-sku').selectOption('flyers');

  // loading presets for selected product
  await page.waitForResponse(/\/pdc\/v1\/products/, {
    timeout: 1000,
  });

  // select preset
  await page.getByTestId('pdc-preset-id').selectOption(presetID);

  // pdf file = fixture
  await page.getByRole('link', { name: 'Choose file' }).click();
  await page.getByRole('tab', { name: 'Upload files' }).click();
  const fileChooserPromise = page.waitForEvent('filechooser');
  await page.getByRole('button', { name: 'Select Files' }).click();
  const fileChooser = await fileChooserPromise;
  await fileChooser.setFiles(path.join(__dirname, `/fixtures/pdc_flyera5.pdf`));
  await page.getByRole('button', { name: 'Select File', exact: true }).click();

  await page.getByRole('button', { name: 'Update' }).click();
  await page.waitForURL(productURL);
}

interface ConfigureVariableProductParams {
  productID: string;
  variations: {
    variationID: string;
    sku: string;
    preset: string;
  }[];
}
export async function configureVariableProduct(page, params: ConfigureVariableProductParams) {
  const productURL = `/wp-admin/post.php?post=${params.productID}&action=edit`;
  await page.goto(productURL);
  await page.getByRole('link', { name: 'Variations' }).click();
  await page.waitForResponse('**/admin-ajax.php');

  for (let i = 0; i < params.variations.length; i++) {
    const variation = params.variations[i];
    await page.locator(`.woocommerce_variation:has-text('#${variation.variationID}')`).click();
    await page.getByTestId(`variation_sku_${variation.variationID}`).selectOption(params.variations[i].sku);

    await page.waitForResponse(/\/pdc\/v1\/products/, {
      timeout: 1000,
    });

    await page.getByTestId(`variation_preset_${variation.variationID}`).selectOption(params.variations[i].preset);
    await page.getByTestId(`variation_file_${variation.variationID}`).click();
    await page.getByRole('tab', { name: 'Upload files' }).click();
    const fileChooserPromise = page.waitForEvent('filechooser');
    await page.getByRole('button', { name: 'Select Files' }).click();
    const fileChooser = await fileChooserPromise;
    await fileChooser.setFiles(path.join(__dirname, `/fixtures/pdc_flyera5.pdf`));
    await page.getByRole('button', { name: 'Select File', exact: true }).click();
  }

  await page.getByRole('button', { name: 'Update' }).click();
  await page.waitForURL(productURL);
}
