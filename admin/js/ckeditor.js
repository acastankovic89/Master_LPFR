function renderOpenBrowserImageButton() {

  var html  = '<a class="cke_dialog_ui_button cke_dialog_ui_button_ok open-media-modal-texteditor open-media-modal" title="Browse" data-type="' + MODAL_TYPE.TEXT_EDITOR_IMAGE + '" style="margin-left: 7px;">';
        html += '<span class="cke_dialog_ui_button">Browse</span>';
      html += '</a>';
  return html;
};

function renderOpenBrowserVideoButton() {

  var html  = '<a class="cke_dialog_ui_button cke_dialog_ui_button_ok open-media-modal-texteditor-video open-media-modal" title="Browse" data-type="' + MODAL_TYPE.CONTENT_VIDEO + '" style="margin-left: 7px;">';
        html += '<span class="cke_dialog_ui_button">Browse</span>';
      html += '</a>';
  return html;
};

function initTextEditor() {

  if($('.cke-editor').length != 0) {

    $('.cke-editor').each(function () {
      var idAttr = $(this).attr('id');
      CKEDITOR.replace(idAttr);
    });

    // CKEDITOR.replace('editor');
    CKEDITOR.config.disableAutoInline = true;
    CKEDITOR.config.allowedContent = true;

    if ($('#articleInsertPage').length > 0) {
      CKEDITOR.config.extraPlugins = 'html5video';
      CKEDITOR.config.extraPlugins = 'gallery';
    }

    CKEDITOR.on('dialogDefinition', function (e) {
      var dialogName = e.data.name;
      var dialog = e.data.definition.dialog;

      dialog.on('show', function () {
        if (dialogName == 'image') {
          var dialogFirstInput = this.getElement().find('input').getItem(0);
          $('#' + dialogFirstInput.$.id).addClass('text-editor-image-field');
          if ($('.open-media-modal-texteditor').length == 0) {
            var btn = renderOpenBrowserImageButton();
            $(btn).insertAfter('#' + dialogFirstInput.$.id);
          }
        }

        if(dialogName == 'html5video') {
          var dialogFirstInput = this.getElement().find('input').getItem(0);
          $('#' + dialogFirstInput.$.id).addClass('text-editor-video-field');
          if ($('.open-media-modal-texteditor-video').length == 0) {
            var btn = renderOpenBrowserVideoButton();
            $(btn).insertAfter('#' + dialogFirstInput.$.id);
          }
        }
      });
      dialog.on('hide', function () {
      });
    });
  }
};