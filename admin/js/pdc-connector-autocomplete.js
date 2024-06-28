jQuery(function ($) {
  $('#js-pdc-product-search')
    .autocomplete({
      select: function (event, ui) {
        // Set selected values to input fields
        $('#js-pdc-product-search').val(ui.item.title); // Set display name
        $('#js-pdc-product-sku').val(ui.item.sku); // Set SKU
        $('#js-pdc-preset-search').attr('disabled', false); // Enable preset search
        $('#js-pdc-preset-search').val('');
        $('#js-pdc-preset-id').val('');
        // search for presets for the selected product
        $('#js-pdc-preset-search').autocomplete('search');
        return false;
      },
      change: function (event, ui) {
        if (!ui.item || !ui.item.sku) {
          $('#js-pdc-product-search').val('');
          $('#js-pdc-product-sku').val('');
          return false;
        }
      },
      source: async function (request, response) {
        try {
          $('#js-pdc-product-search-spinner').addClass('is-active');
          const products = await searchProducts(request.term);
          response(products);
        } finally {
          $('#js-pdc-product-search-spinner').removeClass('is-active');
        }
      },
    })
    .autocomplete('instance')._renderItem = function (ul, item) {
    return $('<li class="pdc-autocomplete-item">')
      .append('<span>' + item.title + '</span>')
      .appendTo(ul);
  };

  $('#js-pdc-preset-search')
    .autocomplete({
      minLength: 0,
      select: function (event, ui) {
        if (!ui.item.preset_id) return false;
        $('#js-pdc-preset-search').val(ui.item.title);
        $('#js-pdc-preset-id').val(ui.item.preset_id);
        return false;
      },
      change: function (event, ui) {
        if (!ui.item || !ui.item.preset_id) {
          $('#js-pdc-product-search').val('');
          $('#js-pdc-preset-id').val('');
        }
      },
      response: function (event, ui) {
        if (!ui.content.length) {
          const productTitle = $('#js-pdc-product-search').val();
          const productSKU = $('#js-pdc-product-sku').val();
          ui.content.push({
            value: '',
            title: `No presets configured. Go to <a target="_blank" href="https://app.print.com/selector/${productSKU}">${productTitle}</a> to configure a preset.`,
          });
        }
      },
      source: async function (request, response) {
        try {
          const sku = $('#js-pdc-product-sku').val();
          const presets = await searchPresetsBySKU(sku);
          response(presets);
        } catch (err) {
          console.log(err);
        } finally {
          $('#js-pdc-preset-search-spinner').removeClass('is-active');
        }
      },
    })
    .autocomplete('instance')._renderItem = function (ul, item) {
    return $('<li class="pdc-autocomplete-item">')
      .append('<span>' + item.title + '</span>')
      .appendTo(ul);
  };

  $('#js-pdc-preset-search').on('focus', function () {
    $('#js-pdc-preset-search').autocomplete('search');
  });
  async function searchPresetsBySKU(sku) {
    return new Promise((resolve, reject) => {
      $.ajax({
        dataType: 'json',
        url: pdcAdminApi.root + 'pdc/v1/products/' + sku + '/presets',
        data: {
          security: pdcAdminApi.nonce,
          sku,
        },
        error: function (err) {
          reject(err);
        },
        success: function (data) {
          resolve(data);
        },
      });
    });
  }

  async function searchProducts(searchterm) {
    return new Promise((resolve, reject) => {
      $.ajax({
        dataType: 'json',
        url: pdcAdminApi.root + 'pdc/v1/products/',
        data: {
          term: searchterm,
          security: pdcAdminApi.nonce,
        },
        success: function (data) {
          resolve(data);
        },
        error: function (err) {
          reject(err);
        },
      });
    });
  }
});
