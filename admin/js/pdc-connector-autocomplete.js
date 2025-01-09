function debounce(fn, wait) {
  let timeout;
  return function (...args) {
    return new Promise((resolve) => {
      clearTimeout(timeout);

      const later = function () {
        timeout = null;
        resolve(fn(...args));
      };
      timeout = setTimeout(later, wait);
    });
  };
}

jQuery(function ($) {
  function $el(selector) {
    return $(selector).first();
  }

  function variationHasChanged() {
    $(this).closest('.woocommerce_variation').addClass('variation-needs-update');
    $('button.cancel-variation-changes, button.save-variation-changes').prop('disabled', false);
    $('#variable_product_options').trigger('woocommerce_variations_input_changed');
  }

  const listProductsDebounced = debounce(listProducts, 350);
  const presets = {};
  async function listProducts(searchTerm) {
    return new Promise((resolve, reject) => {
      wp.ajax
        .post('pdc-list-products', {
          searchTerm: searchTerm?.toLowerCase(),
        })
        .done(resolve)
        .fail(reject);
    });
  }

  async function listPresets(sku) {
    return new Promise((resolve, reject) => {
      wp.ajax
        .post('pdc-list-presets', {
          sku: sku,
        })
        .done(resolve)
        .fail(reject);
    });
  }

  async function loadPresets(parentSelector, sku) {
    try {
      $el(`${parentSelector} .js-pdc-preset-search-spinner`).addClass('is-active');
      listPresetsStatus = 'loading';
      const { presets: result } = await listPresets(sku);
      presets[sku] = result;
      listPresetsStatus = 'idle';
      return result;
    } catch (err) {
      listPresetsStatus = 'error';
    } finally {
      $el(`${parentSelector} .js-pdc-preset-search-spinner`).removeClass('is-active');
    }
  }

  initializePresetAutocomplete('#pdc_product_data_tab');

  function initProductAutocomplete() {
    let listProductsStatus = 'idle';
    const defaultValueProduct = $el(`#js-pdc-product-sku`).val()
      ? {
          sku: $el(`#js-pdc-product-sku`).val(),
          title: $el(`#js-pdc-product-title`).val(),
        }
      : undefined;
    const productListAutocomplete = $el(`#js-pdc-ac-product-list`);
    if (!productListAutocomplete.length) return;

    accessibleAutocomplete({
      element: productListAutocomplete[0],
      id: 'pdc-products-label', // To match it to the existing <label>.
      confirmOnBlur: false,
      defaultValue: defaultValueProduct?.title,
      onConfirm: (item) => {
        if (!item) {
          $el(`#js-pdc-preset-search`).attr('disabled', true);
          return;
        }
        $el(`#js-pdc-preset-search`).removeAttr('disabled');
        $el(`#js-pdc-product-sku`).val(item.sku);
        $el(`#js-pdc-product-title`).val(item.title);
        loadPresets('', item.sku);
        variationHasChanged();
      },
      templates: {
        inputValue: (item) => {
          if (!item) {
            return undefined;
          }
          if (typeof item === 'string') {
            return defaultValueProduct.title;
          }
          return item.title;
        },
        suggestion: (res) => {
          if (!res) {
            return undefined;
          }
          if (typeof res === 'string') {
            return `<span>${defaultValueProduct?.title}</span>&nbsp;<code>${defaultValueProduct.sku}</code>`;
          }
          return `<span>${res?.title}</span>&nbsp;<code>${res.sku}</code>`;
        },
      },
      tNoResults: function tNoResults() {
        if (listProductsStatus === 'loading') {
          return 'Loading suggestions...';
        } else if (listProductsStatus === 'error') {
          return 'Sorry, an error occurred';
        } else {
          return 'No results found';
        }
      },
      source: async (query, populateResults) => {
        try {
          loadingProducts = 'loading';
          $el(`#js-pdc-product-search-spinner`).addClass('is-active');
          const { products } = await listProductsDebounced(query);
          populateResults(products);
          listProductsStatus = 'idle';
        } catch (err) {
          listProductsStatus = 'error';
          console.error('err:', err);
          populateResults([]);
        } finally {
          $el(`#js-pdc-product-search-spinner`).removeClass('is-active');
        }
      },
    });
  }
  initProductAutocomplete();

  $('#woocommerce-product-data').on('woocommerce_variations_loaded', () => {
    $('.woocommerce_variation .pdc_product_options').each((index) => {
      const elID = $('.woocommerce_variation .pdc_product_options')[index].id;
      initializePresetAutocomplete(`#${elID}`);
    });
  });

  function initializePresetAutocomplete(parentSelector) {
    let listPresetsStatus = 'idle';

    const presetListAutocomplete = $el(`${parentSelector} .pdc-ac-preset-list`);
    if (!presetListAutocomplete.length) return;

    const defaultValuePreset = $el(`${parentSelector} .js-pdc-preset-id`).val()
      ? {
          sku: $el(`${parentSelector} .js-pdc-preset-id`).val(),
          title: $el(`${parentSelector} .js-pdc-preset-title`).val(),
        }
      : undefined;
    accessibleAutocomplete({
      element: presetListAutocomplete[0],
      showAllValues: true,
      id: 'pdc-presets-label', // To match it to the existing <label>.
      defaultValue: defaultValuePreset?.title,
      source: async function (query, populateResults) {
        const sku = $el(`#js-pdc-product-sku`).val();
        const presetsForSku = presets[sku] || [];
        if (presetsForSku.length === 0) {
          await loadPresets(parentSelector, sku);
        }
        populateResults(presets[sku] || []);
      },
      onConfirm: (item) => {
        if (!item) {
          return;
        }
        $el(`${parentSelector} .js-pdc-preset-id`).val(item.id);
        $el(`${parentSelector} .js-pdc-preset-title`).val(item.title);
        variationHasChanged();
      },
      tNoResults: function tNoResults() {
        if (listPresetsStatus === 'loading') {
          return 'Loading presets...';
        } else if (listPresetsStatus === 'error') {
          return 'Sorry, an error occurred';
        } else {
          const sku = $el(`#js-pdc-product-sku`).val();
          return `No presets for ${sku}`;
        }
      },
      templates: {
        inputValue: (item) => {
          if (!item) {
            return undefined;
          }
          return item.title;
        },
        suggestion: (res) => {
          if (!res) return undefined;
          return `<span>${res?.title}</span>`;
        },
      },
    });
  }
});
