// DOM elements
var $mediaModal = $('#mediaModal');
var $tab = $mediaModal.find('.tab');
var $content = $mediaModal.find('.content');
var $imagesWrapper = $mediaModal.find('.images-wrapper');
var $modalLoader = $mediaModal.find('.modal-inner-overlay');
var $modalSaveBtn = $('.modal-save');
var $youtubeModal = $('#youtubeVideoModal');
var $galleriesModal = $('#galleriesModal');

var itemIntroImageId = '#itemIntroImage-langId-';
var introImageWrapperId = '#introImageWrapper-langId-';
var itemMainImageId = '#itemMainImage-langId-';
var mainImageWrapperId = '#mainImageWrapper-langId-';
var galleryImageWrapperId = '#galleryImageWrapper-langId-';


function openModal(type, langId) {

  if(type == MODAL_TYPE.GALLERY_YOUTUBE_VIDEO) {

    openYoutubeModal(type, langId);

  } else {

    var currentType = $mediaModal.attr('data-type');

    $mediaModal.attr({'data-type': type, 'data-lang_id': langId});

    if(currentType != type) {
      changeModalWarningMessage(type);
      displayModalItems();
    }

    openMediaModal(type);
  }
};


function openMediaModal(type) {
  $mediaModal.attr('data-type', type);
  $mediaModal.parents('.modal-overlay').fadeIn();
  $mediaModal.addClass('show');
};


function hideMediaModal() {
  $mediaModal.removeAttr('data-type');
  $mediaModal.removeAttr('data-lang_id');
  $mediaModal.removeClass('show');
  $mediaModal.parents('.modal-overlay').fadeOut();
};


function openYoutubeModal(type, langId) {
  $youtubeModal.attr({'data-type': type, 'data-lang_id': langId});
  $youtubeModal.parents('.modal-overlay').fadeIn();
  $youtubeModal.addClass('show');
};


function hideYoutubeModal() {
  $youtubeModal.removeAttr('data-type');
  $youtubeModal.removeAttr('data-lang_id');
  $youtubeModal.removeClass('show');
  $youtubeModal.parents('.modal-overlay').fadeOut();
  $youtubeModal.find('.youtube-video-field').val('');
  $youtubeModal.find('.youtube-video-field').parents('.field-wrapper').removeClass('warning');
  $youtubeModal.find('.youtube-video-field').parents('.field-wrapper').removeClass('focus');
};


function showGalleriesModal() {
  $galleriesModal.parents('.modal-overlay').fadeIn();
  $galleriesModal.addClass('show');
};


function hideGalleriesModal() {
  $galleriesModal.removeClass('show');
  $galleriesModal.parents('.modal-overlay').fadeOut();
};


function showModalLoading() {
  $modalLoader.show();
};


function hideModalLoading() {

  setTimeout(function () {
    $modalLoader.fadeOut();
  }, 800);
};


var ncDropzone;

function initDropZone() {

  ncDropzone = new Dropzone('#ncDropzone',
    {
      url: API_URL + '/media/upload',
      maxFiles: 1, // Maximum Number of Files
      maxFilesize: 200,
      dictDefaultMessage: 'Drop files here to upload (or click).'
    }
  );

  ncDropzone.on('error', function (file, response) {
    console.log(file);
    console.log(response);
  })
    .on('success', function (file, response) {

      console.log(response);

      var modalType = $mediaModal.attr('data-type');
      var modalLangId = $mediaModal.attr('data-lang_id');

      setTimeout(
        function () {

          if (exists(response.file)) {
            displayPageMediaItem(response.file, modalType, modalLangId);
          }

          hideMediaModal();
          //ncDropzone.removeFile(response.file);
          ncDropzone.removeAllFiles(true);

        }, 800
      );
    })
    .on('error', function (file, response) {
      var self = this;
      var file = file;
      setTimeout(
        function () {

          hideMediaModal();

          if (exists(file)) {
            self.removeFile(file);
          }

        }, 1200
      );
    });
};


function setMimeType() {

  var modalType = $mediaModal.attr('data-type');

  var mimeType = 'image';

  if (modalType === MODAL_TYPE.CONTENT_VIDEO) {
    mimeType = 'video';
  }

  return mimeType;
};


function displayModalItems(page = null) {

  showModalLoading();

  if (typeof page == 'undefined' || page == null) {
    page = 1;
  }

  var mimeType = setMimeType();

  var payload = new FormData();

  payload.append('page', page);
  payload.append('items_per_page', ITEMS_PER_PAGE.MODAL_IMAGES);
  payload.append('mime_type', mimeType);
  payload.append('order_by', 'id');
  payload.append('order_direction', 'desc');

  axios.post(API_URL + '/media/fetch-with-filters', payload, AXIOS_HEADERS)
    .then(function (response) {

      removeModalItems();

      var total = response.data.total;
      var items = response.data.items;

      var html = 'Files don\'t exist try uploading some';

      if (total != 0) {

        html = renderModalPagination(total, page);
        html += renderModalItems(items);
        html += renderModalPagination(total, page);

        appendModalItems(html);
      }
    })
    .catch(function (error) {
      console.log(error);
    })
    .finally(function () {
      hideModalLoading();
    });

};


function renderModalPagination(total, page) {

  var pagination = Math.ceil(total / ITEMS_PER_PAGE.MODAL_IMAGES);

  if (pagination == 0 || pagination == 1) return '<div class="modal-pagination"></div>';

  var html = '<div class="modal-pagination">';
    html += '<ul class="pagination">';

    for (var i = 1; i <= pagination; i++) {

      var activeClass = page == i ? ' class="active"' : '';

      html += '<li' + activeClass + '><a href="#" data-page="' + i + '">' + i + '</a></li>';
    }

    html += '</ul>';
  html += '</div>';

  return html;
};


function renderModalItems(data) {

  var html = '<div class="nc-row nc-cols-8">';

  for (var key in data) {

    var media = data[key];

    var image = ADMIN_URL + '/css/img/no-image.png';
    if(media.mime_type == 'image') {
      image = MEDIA_THUMBS_URL + '/' + media.file_name;
    } else if(media.mime_type == 'audio') {
      image = ADMIN_URL + '/css/img/sound.png';
    } else if(media.mime_type == 'video') {
      image = ADMIN_URL + '/css/img/video.png';
    }

    html += '<a href="#" data-id="' + media.id + '" data-title="' + media.title + '" data-file_name="' + media.file_name + '" class="add-media nc-col">';
      html += '<div class="item">';
        html += '<div class="image-wrapper"><img src="' + image + '" alt="' + media.title + '" /></div>';
        html += '<div class="file">' + media.title + '</div>';
      html += '</div>';
    html += '</a>';
  }

  html += '</div>';

  return html;
};


function renderPageImage(fileName, modalType, langId) {

  var html = '<div class="panel-image-wrapper" style="background-image: url(' + MEDIA_URL + '/' + fileName + ')">';
    html += '<button class="btn btn-sm remove-image" data-type="' + modalType + '" data-lang_id="' + langId + '"><i class="fas fa-times"></i></button>';
  html += '</div>';

  return html;
};


function renderGalleryItem(value, modalType, langId) {

  var descLabel = 'Add description';

  var html = '<div class="nc-col">';

    html += '<div class="g-item" data-type="' + modalType + '">';

      html += '<button class="btn btn-sm remove-item"><i class="fas fa-times"></i></button>';

      html += '<div class="image-wrapper">';
        if(modalType == MODAL_TYPE.GALLERY_IMAGE) {

          html += '<img src="' + MEDIA_URL + '/' + value + '" alt="" />';

        } else if(modalType == MODAL_TYPE.GALLERY_YOUTUBE_VIDEO) {

          value = getYouTubeVideoCode(value);

          html += '<img src="http://img.youtube.com/vi/' + value + '/0.jpg" alt="" />';
          html += '<img src="' + ADMIN_URL + '/css/img/icon-youtube.png" class="icon" alt="" />';
        }

      html += '</div>';

      html += '<div class="description-wrapper">';
        html += '<a href="#" class="add-description"></a>';
        html += '<div class="description">' + descLabel + '</div>';
        html += '<textarea class="description-value"></textarea>';
        html += '<button type="button" class="btn save-description">Save</button>';
      html += '</div>';

      html += '<input type="hidden" class="g-item-value" value="' + value + '" />';

    html += '</div>';

  html += '</div>';

  $(galleryImageWrapperId + langId).find('.gallery').append(html);
};


function removeModalItems() {
  $imagesWrapper.html('');
};


function appendModalItems(html) {
  $imagesWrapper.append(html);
};


function renderPageIntroImage(fileName, modalType, langId) {
  var image = renderPageImage(fileName, modalType, langId);
  $(introImageWrapperId + langId).html(image);
};


function renderPageMainImage(fileName, modalType, langId) {
  var image = renderPageImage(fileName, modalType, langId);
  $(mainImageWrapperId + langId).html(image);
};


function displayTextEditorMediaUrl(fileName) {
  var url = MEDIA_URL + '/' + fileName;
  var urlInput = $('.cke_dialog .text-editor-image-field');
  var altInput = $('.cke_dialog input:eq(1)');
  $(urlInput).click();
  $(urlInput).focus();
  $(urlInput).val(url);
  $(urlInput).trigger('change');
  $(urlInput).blur();
  $(altInput).click();
  $(altInput).focus();
};


function displayTextEditorVideoMediaUrl(fileName) {
  var url = MEDIA_URL + '/' + fileName;
  var urlInput = $('.cke_dialog .text-editor-video-field');
  $(urlInput).click();
  $(urlInput).focus();
  $(urlInput).val(url);
  $(urlInput).trigger('change');
  $(urlInput).blur();
};


function displayTextEditorGalleryShortCode(id) {
  var shortCode = '{' + NC_GALLERY_LABEL + '=' + id + '}';
  var shortCodeField = $('.cke_dialog input:first');
  $(shortCodeField).click();
  $(shortCodeField).focus();
  $(shortCodeField).val(shortCode);
  $(shortCodeField).trigger('change');
  $(shortCodeField).blur();
}


function displayPageMediaItem(fileName, modalType, langId) {

  if (modalType == MODAL_TYPE.INTRO_IMAGE) {

    renderPageIntroImage(fileName, modalType, langId);
    $(itemIntroImageId + langId).val(fileName);

  } else if (modalType == MODAL_TYPE.MAIN_IMAGE) {

    renderPageMainImage(fileName, modalType, langId);
    $(itemMainImageId + langId).val(fileName);

  } else if (modalType == MODAL_TYPE.TEXT_EDITOR_IMAGE) {

    displayTextEditorMediaUrl(fileName, langId);

  } else if (modalType == MODAL_TYPE.GALLERY_IMAGE || modalType == MODAL_TYPE.GALLERY_YOUTUBE_VIDEO) {

    renderGalleryItem(fileName, modalType, langId);

  } else if (modalType == MODAL_TYPE.CONTENT_VIDEO) {

    displayTextEditorVideoMediaUrl(fileName, langId);
  }

};


function removePageIntroImage(langId) {
  $(introImageWrapperId + langId).html(' ');
  $(itemIntroImageId + langId).val('');
};


function removePageMainImage(langId) {
  $(mainImageWrapperId + langId).html(' ');
  $(itemMainImageId + langId).val('');
};


function removePageImage(modalType, langId) {

  if (modalType == MODAL_TYPE.INTRO_IMAGE) {
    removePageIntroImage(langId);
  } else if (modalType == MODAL_TYPE.MAIN_IMAGE) {
    removePageMainImage(langId);
  }
};


function showModalImageMessage() {
  $('.media-modal-message.image').show();
};


function showModalDocumentMessage() {
  $('.media-modal-message.document').show();
};


function hideModalWarningMessages() {
  $('.media-modal-message.image').hide();
  $('.media-modal-message.document').hide();
};


function changeModalWarningMessage(modalType) {

  hideModalWarningMessages();

  if (modalType == MODAL_TYPE.DOCUMENT) {
    showModalDocumentMessage();
  } else {
    showModalImageMessage();
  }
};


// EVENT LISTENERS
$(document).ready(function () {

  initDropZone();
  //displayModalItems();
});


$(document).on('click', '.open-media-modal', function (event) {
  eventPreventDefault(event);

  var type = $(this).attr('data-type');
  var langId = $(this).attr('data-lang_id');

  openModal(type, langId);
});


$tab.on('click', function () {

  var id = $(this).data('id');

  $tab.removeClass('active');
  $(this).addClass('active');

  $content.removeClass('active');
  $('#content' + id).addClass('active');

  if (id == 2) {
    displayModalItems();
  }

});


$(document).on('click', '.modal-pagination a', function (event) {
  eventPreventDefault(event);

  var page = $(this).attr('data-page');

  displayModalItems(page);
});


$(document).on('click', '.add-media', function (event) {
  eventPreventDefault(event);

  var fileName = $(this).attr('data-file_name');

  var modalType = $mediaModal.attr('data-type');
  var langId = $mediaModal.attr('data-lang_id');

  hideMediaModal();
  displayPageMediaItem(fileName, modalType, langId);
  $mediaModal.removeAttr('data-type');
  $mediaModal.removeAttr('data-lang_id');
});


$(document).on('click', '.remove-image', function (event) {
  eventPreventDefault(event);

  var type = $(this).attr('data-type');
  var langId = $(this).attr('data-lang_id');

  removePageImage(type, langId);
});


$modalSaveBtn.on('click', function (event) {
  eventPreventDefault(event);

  var type = $(this).parents('.modal').attr('data-type');
  var langId = $(this).parents('.modal').attr('data-lang_id');

  if(type == MODAL_TYPE.GALLERY_YOUTUBE_VIDEO) {

    var value = $youtubeModal.find('.youtube-video-field').val();

    if(value == '') {

      $youtubeModal.find('.youtube-video-field').parents('.field-wrapper').addClass('warning');

    }  else {

      displayPageMediaItem(value, type, langId);
      hideYoutubeModal();
    }
  }
});


$(document).on('click', '.open-gallery-modal',
  function (event) {
    eventPreventDefault(event);

    showGalleriesModal();
  }
);


$(document).on('click', '.add-gallery',
  function (event) {
    eventPreventDefault(event);

    var id = $(this).attr('data-id');

    displayTextEditorGalleryShortCode(id);
    hideGalleriesModal();
  }
);