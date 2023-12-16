const PLUGIN_NAME = pdcAdminApi.plugin_name;

(function ($) {
  'use strict';

  function getCredentials() {
    const username = $(`#js-${PLUGIN_NAME}-testusername`).val();
    const password = $(`#js-${PLUGIN_NAME}-testpw`).val();
    return {
      username,
      password,
    };
  }

  async function checkCredentials() {
    $(`#js-${PLUGIN_NAME}-auth-success`).hide();
    $(`#js-${PLUGIN_NAME}-auth-failed`).hide();

    const { username, password } = getCredentials();

    if (!username || !password) {
      alert('Missing username or password');
      return;
    }

    const response = await fetch(pdcAdminApi.pdc_url + 'login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        credentials: {
          username,
          password,
        },
      }),
    });
    if (response.status !== 200) {
      $(`#js-${PLUGIN_NAME}-auth-failed`).show();
      $(`#js-${PLUGIN_NAME}-auth-success`).hide();
      return;
    }
    $(`#js-${PLUGIN_NAME}-auth-failed`).hide();
    $(`#js-${PLUGIN_NAME}-auth-success`).show();
  }

  // On order item detail page, will allow adding a
  // PDF file to the order item
  function orderItemAttachPdf(e) {
    e.preventDefault();
    const orderItemId = e.target.getAttribute('data-order-item-id');
    
    var frame = wp.media({
      title: 'Select or Upload a Custom File',
      button: {
        text: 'Use this file',
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
      } catch (err) {
        console.log(err);
      }
      $('#_pdc_pdf_url').val(attachment.url);
    });

    frame.open();
  }

  // On order item detail page, will purchase
  // the order item with Print.com
  let loading = false;
  async function purchaseOrderItem(e) {
    e.preventDefault();
    if (loading) return;
    loading = true;
    $('#pdc-order').addClass('button-disabled');
    $("#js-pdc-action-spinner").addClass('is-active');
    $("#js-pdc-request-response").text('');
    const orderItemId = e.target.getAttribute('data-order-item-id');
    try {
      await $.ajax(
        {
          method: 'POST',
          url: `${pdcAdminApi.root}pdc/v1/orders/`,
          beforeSend(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', pdcAdminApi.nonce);
          },
          data: {
            orderItemId,
          },
        },
        {}
      );
    } catch (err) {
      $("#js-pdc-request-response").text(err.responseJSON.message);
    } finally {
      loading = false;
      $('#pdc-order').removeClass('button-disabled');
      $("#js-pdc-action-spinner").removeClass('is-active');
    }
  }

  $(window).load(function () {
    $('#pdc-file-upload').on('click', orderItemAttachPdf);
    $('#pdc-order').on('click', purchaseOrderItem);
    $(`#js-${PLUGIN_NAME}-testcredentials`).click(checkCredentials);
  });
})(jQuery);
