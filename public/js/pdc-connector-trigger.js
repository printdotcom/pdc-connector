(function ($) {
  'use strict';

  const editor = $('#pdc-js-editor');
  if (editor) {
    const template = editor.data('data-pdc-template');
    if (!template) return;
    const event = new CustomEvent('cve-loadtemplate', {
      detail: {
        template: template,
      },
    });
    window.dispatchEvent(event);
  }
})(jQuery);
