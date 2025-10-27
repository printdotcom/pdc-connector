(function ($) {
  'use strict';

  const PLUGIN_NAME = pdcAdminApi.plugin_name;

  async function checkCredentials() {
    $(`#js-${PLUGIN_NAME}-auth-success`).hide();
    $(`#js-${PLUGIN_NAME}-auth-failed`).hide();

    const pdcApiKey = $(`#pdc_api_key`).val();

    if (!pdcApiKey) {
      alert('No API Key entered');
      return;
    }

    if (formIsDirty) {
      alert('Please save the settings before verifying the API key');
      return;
    }

    try {
      $(`#js-${PLUGIN_NAME}-verify_key`).prop('disabled', true);
      $(`#js-${PLUGIN_NAME}-verify_loader`).addClass('is-active');

      const response = await fetch(pdcAdminApi.pdc_url + '/products', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `PrintApiKey ${pdcApiKey}`,
        },
      });
      if (response.status !== 200) {
        $(`#js-${PLUGIN_NAME}-auth-failed`).show();
        $(`#js-${PLUGIN_NAME}-auth-success`).hide();
        return;
      }
      $(`#js-${PLUGIN_NAME}-auth-failed`).hide();
      $(`#js-${PLUGIN_NAME}-auth-success`).show();
    } catch (err) {
      $(`#js-${PLUGIN_NAME}-auth-failed`).show();
    } finally {
      $(`#js-${PLUGIN_NAME}-verify_key`).prop('disabled', false);
      $(`#js-${PLUGIN_NAME}-verify_loader`).removeClass('is-active');
    }
  }

  // On order item detail page, will allow adding a
  // PDF file to the order item
  function orderItemAttachPdf(e) {
    e.preventDefault();
    const orderItemId = e.target.getAttribute('data-order-item-id');

    const frame = wp.media({
      title: 'Select or Upload a Custom File',
      button: {
        text: 'Use this file',
      },
      library: {
        type: 'document',
        post_mime_type: ['application/pdf'],
      },
      multiple: false,
    });

    frame.on('select', async function () {
      const attachment = frame.state().get('selection').first().toJSON();
      try {
        await $.ajax(
          {
            method: 'POST',
            url: `${pdcAdminApi.root}pdc/v1/orders/${orderItemId}/attach-pdf`,
            beforeSend(xhr) {
              xhr.setRequestHeader('X-WP-Nonce', pdcAdminApi.nonce);
            },
            data: {
              orderItemId,
              pdfUrl: attachment.url,
            },
          },
          {}
        );
        await refreshOrderItem(orderItemId);
        $('#js-pdc-order-pdf').val(attachment.url);
      } catch (err) {
        $('#js-pdc-request-response').text(err.responseJSON.message);
      }
    });

    frame.open();
  }

  function refreshOrderItem(orderItemId) {
    const orderItemRow = $(`#pdc_order_item_${orderItemId}`);
    if (!orderItemRow.length) return;
    return new Promise((resolve) => {
      orderItemRow.load(`${document.URL} #pdc_order_item_${orderItemId}_inner`, function () {
        resolve();
      });
    });
  }

  // On order item detail page, will purchase
  // the order item with Print.com
  let loading = false;
  async function purchaseOrderItem(e) {
    e.preventDefault();
    if (loading) return;
    loading = true;
    $('#pdc-order').addClass('button-disabled');
    $('#js-pdc-action-spinner').addClass('is-active');
    $('#js-pdc-request-response').text('');
    const orderItemId = e.target.getAttribute('data-order-item-id');
    try {
      const response = await fetch(`${pdcAdminApi.root}pdc/v1/orders/${encodeURIComponent(orderItemId)}/purchase`, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': pdcAdminApi.nonce,
        },
      });
      const payload = await response.json().catch(() => ({}));
      if (!response.ok) {
        const message = payload?.message || payload?.data?.message || 'Failed to place order.';
        throw new Error(message);
      }
      await refreshOrderItem(orderItemId);
    } catch (err) {
      $('#js-pdc-request-response').text(err.message || 'Failed to place order.');
    } finally {
      loading = false;
      $('#pdc-order').removeClass('button-disabled');
      $('#js-pdc-action-spinner').removeClass('is-active');
    }
  }

  let formIsDirty = false;
  function observeFormChanges(formID) {
    const formElement = $(formID);
    if (!formElement.length) return;

    $(`${formID} input, ${formID} select`).on('change', function () {
      formIsDirty = true;
    });
  }

  $(window).load(function () {
    $('#pdc-file-upload').on('click', orderItemAttachPdf);
    $('#pdc-order').on('click', purchaseOrderItem);
    $(`#js-${PLUGIN_NAME}-verify_key`).click(checkCredentials);
    observeFormChanges(`#js-${PLUGIN_NAME}-general-form`);
  });

  // Upload file button click event for simple products
  function openMediaDialogFromProduct(e) {
    openMediaDialog(e, function (attachment) {
      $(`#${e.target.dataset.pdcVariationFileField}`).val(attachment.url);
      $('.woocommerce_variation').addClass('variation-needs-update');
      $('button.cancel-variation-changes, button.save-variation-changes').prop('disabled', false);
      $('#variable_product_options').trigger('woocommerce_variations_input_changed');
    });
  }
  function openMediaDialogFromOrder(e) {
    openMediaDialog(e, function (attachment) {
      $('#_pdc_file_id').val(attachment.id);
      $('#_pdc-file_url').val(attachment.url);
    });
  }

  function openMediaDialog(e, onSelect) {
    e.preventDefault();
    const mediaUploadModal = wp.media({
      title: 'Select or Upload a PDF',
      button: {
        text: 'Select File',
      },
      library: {
        type: 'document',
        post_mime_type: ['application/pdf'],
      },
      multiple: false,
    });

    mediaUploadModal.on('select', function () {
      const attachment = mediaUploadModal.state().get('selection').first().toJSON();
      onSelect(attachment);
    });

    mediaUploadModal.open();
  }

  // rehook dom elements when variations are loaded
  $(document).on('woocommerce_variations_loaded', function onVariationsLoaded() {
    loadPresetsForSKU();
    $('.pdc-connector-js-upload-custom-file-btn').on('click', openMediaDialogFromProduct);
  });

  async function loadPresetsForSKU() {
    const sku = $('#js-pdc-product-selector').val();
    if (!sku) return;
    try {
      const response = await fetch(`${pdcAdminApi.root}pdc/v1/products/${encodeURIComponent(sku)}/presets`, {
        method: 'GET',
        headers: {
          'X-WP-Nonce': pdcAdminApi.nonce,
        },
      });
      const payload = await response.json();
      if (!response.ok) {
        throw new Error(payload?.message || 'Failed to load presets.');
      }
      const presetOptionsHTML = payload?.html || '';
      document.getElementById('js-pdc-preset-list').innerHTML = presetOptionsHTML;

      const variationPresetInputs = document.querySelectorAll('.pdc_variation_preset_select');
      variationPresetInputs.forEach((selectInput) => {
        selectInput.innerHTML = presetOptionsHTML;
      });
    } catch (err) {
      console.error('Failed to load presets', err);
    }
  }

  $(document).ready(function () {
    $('#js-pdc-product-selector').on('change', loadPresetsForSKU);
    $('#pdc-product-file-upload').on('click', openMediaDialogFromOrder);
    $('.pdc-connector-js-upload-custom-file-btn').on('click', openMediaDialogFromProduct);
  });
})(jQuery);
