jQuery(function ($) {
  $('#js-pdc-product-search')
    .autocomplete({
      select: function (event, ui) {
        // Set selected values to input fields
        $('#js-pdc-product-search').val(ui.item.title); // Set display name
        $('#js-pdc-product-sku').val(ui.item.sku); // Set SKU
        $('#js-pdc-preset-search').attr('disabled', false); // Enable preset search
        return false;
      },
      source: function (request, response) {
        $('#js-pdc-product-search-spinner').addClass('is-active');
        $.ajax({
          dataType: 'json',
          url: pdcAdminApi.root + 'pdc/v1/products/',
          data: {
            term: request.term,
            security: pdcAdminApi.nonce,
          },
          success: function (data) {
            $('#js-pdc-product-search-spinner').removeClass('is-active');
            response(data);
          },
        });
      },
    })
    .autocomplete('instance')._renderItem = function (ul, item) {
    return $('<li>')
      .append('<strong>' + item.title + '</strong><span> - ' + item.sku + '</span>')
      .appendTo(ul);
  };

  $('#js-pdc-preset-search')
    .autocomplete({
      minLength: 0,
      select: function (event, ui) {
        $('#js-pdc-preset-search').val(ui.item.title);
        $('#js-pdc-preset-id').val(ui.item.preset_id);
        return false;
      },
      source: function (request, response) {
        $('#js-pdc-preset-search-spinner').addClass('is-active');
        const sku = $('#js-pdc-product-sku').val();
        $.ajax({
          dataType: 'json',
          url: pdcAdminApi.root + 'pdc/v1/products/' + sku + '/presets',
          data: {
            term: request.term,
            security: pdcAdminApi.nonce,
          },
          success: function (data) {
            $('#js-pdc-preset-search-spinner').removeClass('is-active');
            response(data);
          },
        });
      },
      focus(event, ui) {
        console.log('focus');
        return false;
      }
    })
    .autocomplete('instance')._renderItem = function (ul, item) {
      return $('<li>')
        .append('<strong>' + item.title + '</strong>')
        .appendTo(ul);
    };
});
