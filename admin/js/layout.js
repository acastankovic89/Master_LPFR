$(document).ready(function () {
  setNCSelectboxActiveOption();
  setActiveFormFieldsActiveState();
  setSortableItems();
});


$(window).on('load', function () {
  if ($('#loader').length != 0) $('#loader').fadeOut();
  if ($('#overlay').length != 0) $('#overlay').fadeOut();
});

$(window).on('resize', function () {

  var windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

  if(windowWidth >= 768) {
    if($('body').hasClass('sidebar-open')) {
      $('body').removeClass('sidebar-open');
    }
  }
});


/************************** SIDEBAR **************************/

$('#sidebarMenu .toggle').on('click', function (event) {
  eventPreventDefault(event);

  $('#sidebarMenu .toggle').parents('li').find('.dropdown').slideUp();
  $('#sidebarMenu .toggle').removeClass('open');

  $(this).parents('li').find('.dropdown').slideDown();
  $(this).addClass('open');
});

$('#menuOpener').on('click', function (event) {
  eventPreventDefault(event);

  $('body').toggleClass('sidebar-open');

  if($('body').hasClass('sidebar-open')) {

    $('body').addClass('sidebar-open');
  } else {

    $('body').removeClass('sidebar-open');
  }
});


/********************** / END OF SIDEBAR **********************/


/*************************** TOPBAR ***************************/

$('.topbar .user').on('click', function (event) {
  event.stopPropagation();
  $(this).find('.dropdown').toggleClass('open');
});


$('.topbar .user .dropdown').on('click', function (event) {
  eventPreventDefault(event);
});


$('.topbar .user .dropdown a').on('click', function (event) {
  event.stopPropagation();
});


$(document).on('click', function () {
  $('.topbar .user .dropdown').removeClass('open');
});


$('#logout').on('click', function (event) {
  eventPreventDefault(event);

  var payload = $(this).serialize();

  axios.post(API_URL + '/auth/logout', payload, AXIOS_HEADERS)
    .then(function () {
      location.href = BASE_URL + '/administration';
    })
    .catch(function (error) {
      console.log(error);
    });
});

/*********************** / END OF TOPBAR ***********************/


/******************** CUSTOM INPUT FIELD ********************/

$(document)
  .on('focus', '.form-field', function () {

    if (!$(this).hasClass('disabled')) {

      $(this).parents('.field-wrapper').addClass('focus');

      if ($(this).hasClass('nc-select')) {
        $(this).parents('.nc-selectbox').find('.nc-select-options').slideDown();
      }
    }
  })
  .on('blur', '.form-field', function () {

    if (!$(this).hasClass('disabled')) {
      if ($(this).val() === '') {
        $(this).parents('.field-wrapper').removeClass('focus');
      }

      if ($(this).hasClass('nc-select')) {
        $(this).parents('.nc-selectbox').find('.nc-select-options').slideUp();
      }
    }
  });


$('.nc-select-options').on('click', function (event) {
  eventPreventDefault(event);
});

$('.nc-select-options a').on('click', function (event) {
  event.stopPropagation();
  eventPreventDefault(event);

  var $selectBox = $(this).parents('.nc-selectbox');
  if ($selectBox.hasClass('filter')) return;

  var value = $(this).attr('data-value');
  var name = $(this).text();

  $selectBox.find('.nc-select').val(name);
  $selectBox.find('.nc-select-value').val(value);

  $(this).parents('.field-wrapper').addClass('focus');
  $(this).parents('.nc-select-options').slideUp();
});


function setNCSelectboxActiveOption() {

  if ($('.nc-selectbox').length != 0) {

    var $options = $('.nc-selectbox').find('.nc-select-options a');

    $options.each(function () {

      if (typeof $(this).attr('selected') != 'undefined') {
        var id = $(this).attr('data-value');
        var name = $(this).text();

        $(this).parents('.field-wrapper').addClass('focus');
        $(this).parents('.nc-selectbox').find('.nc-select-value').val(id);
        $(this).parents('.nc-selectbox').find('.nc-select').val(name);
      }
    });
  }
};


function setActiveFormFieldsActiveState() {
  $('.form-field').each(function () {

    if ($(this).val() != '') {
      $(this).parents('.field-wrapper').addClass('focus');
    }
  });
};

/***************** / END CUSTOM INPUT FIELD ******************/


/********************* ADD OPTIONS FIELD *********************/

function renderOptionBtn(id, value) {
  return '<div class="option" data-id="' + id + '" data-value="' + htmlentities(value) + '"><span class="text">' + value + '</span><button type="button" class="remove-option"><i class="fas fa-times-circle"></i></button></div>';
};

function setNewOptionId() {

  if ($('.options-field .options .option').length != '') {

    var ids = [];
    $('.options-field .options .option').each(function () {
      var id = $(this).attr('data-id');
      ids.push(id);
    });

    ids.sort();
    var lastId = ids[ids.length - 1];
    return parseInt(lastId) + 1;
  }
  return 0;
}

// options json
function setOptionsJson() {

  var values = [];
  if ($('.options-field .options .option').length != '') {

    $('.options-field .options .option').each(
      function () {

        var value = $(this).attr('data-value');
        values.push(value);
      }
    );
  }

  return JSON.stringify(values);
};

// add options button
$('.options-field .add-option').on('click', function (event) {
  eventPreventDefault(event);

  var $fieldWrapper = $(this).parents('.options-field').find('.field-wrapper');
  var $formField = $(this).parents('.options-field').find('.form-field');

  var value = $formField.val();

  if (value != '') {

    var optionId = $formField.attr('data-option-id');

    if (typeof optionId != 'undefined' && optionId != null) {

      $('.options-field .options .option').each(function () {

        var id = $(this).attr('data-id');
        if (id == optionId) {
          $(this).attr('data-value', htmlentities(value));
          $(this).find('.text').text(value);
        }
      });

    } else {
      var id = setNewOptionId();
      var html = renderOptionBtn(id, value);

      $('.options').append(html);
    }

    $formField.val('');
    $formField.removeAttr('data-option-id');
    $fieldWrapper.removeClass('focus');
  }
});

$(document).on('click', '.option', function (event) {
  eventPreventDefault(event);

  var value = $(this).text();
  var id = $(this).attr('data-id');
  var $fieldWrapper = $(this).parents('.options-field').find('.field-wrapper');
  var $formField = $(this).parents('.options-field').find('.form-field');

  $fieldWrapper.addClass('focus');
  $formField.val(value);
  $formField.attr('data-option-id', id);
});

// remove option button
$(document).on('click', '.remove-option', function (event) {
  eventPreventDefault(event);
  $(this).parents('.option').remove();
});

/******************** / END ADD OPTIONS FIELD ********************/


/************************* CUSTOM MODAL *************************/

function hideMediaModal(elem = null) {

  var $modal = $('.modal');
  var $overlay = $('.modal-overlay');

  if (exists(elem)) {

    $modal = $(elem).parents('.modal');
    $overlay = $(elem).parents('.modal-overlay');
  }

  if (exists($modal.attr('data-type'))) {
    $modal.removeAttr('data-type');
  }

  $modal.removeClass('show');
  $overlay.fadeOut();
};

$('.modal-close, .modal-cancel').on('click', function (event) {
  eventPreventDefault(event);

  // hideMediaModal(this);
  var $modal = $('.modal');
  var $overlay = $('.modal-overlay');

  if (exists(this)) {

    $modal = $(this).parents('.modal');
    $overlay = $(this).parents('.modal-overlay');
  }

  if (exists($modal.attr('data-type'))) {
    $modal.removeAttr('data-type');
  }

  $modal.removeClass('show');
  $overlay.fadeOut();
});

/******************** / END OF CUSTOM MODAL *********************/


/************************ LANGUAGE TABS ************************/

$('.lang-btn').on('click', function (event) {
  eventPreventDefault(event);

  $('.lang-btn').removeClass('active');
  $(this).addClass('active');

  var id = $(this).data('id');

  $('.page-form').hide();
  $('#pageForm-langId-' + id).fadeIn();
});

/******************** / END OF LANGUAGE TABS ********************/


/*************************** GALLERY ***************************/

$('.toggler-wrapper .toggler-header button').on('click', function (event) {
  eventPreventDefault(event);

  var $header = $(this).parents('.toggler-header');
  var $body = $(this).parents('.toggler-wrapper').find('.toggler-body');

  $header.toggleClass('active');

  if($header.hasClass('active')) {
    $body.slideDown();
  } else {
    $body.slideUp();
  }
});

$(document).on('click', '.gallery-wrapper .add-description', function (event) {
  eventPreventDefault(event);

  var $wrapper = $(this).parents('.description-wrapper');

  $wrapper.find('.description').hide();
  $wrapper.find('.description-value').show().focus();
  $wrapper.find('.save-description').show();

});

$(document).on('click', '.gallery-wrapper .save-description', function (event) {
  eventPreventDefault(event);

  var $wrapper = $(this).parents('.description-wrapper');

  var value = $wrapper.find('.description-value').val();
  $wrapper.find('.description').text(truncateString(value, 50));

  $wrapper.find('.description').show();
  $wrapper.find('.description-value').hide();
  $wrapper.find('.save-description').hide();
});

$(document).on('click', '.gallery-wrapper .remove-item', function (event) {
  eventPreventDefault(event);

  $(this).parents('.nc-col').remove();
});

/********************** / END OF GALLERY **********************/


/************************ BACK TO TOP ************************/

$(window).scroll(function () {
  if ($(this).scrollTop() > 200) {
    $('#backTop').fadeIn();
  }
  else {
    $('#backTop').fadeOut();
  }
});

$(document).on('click', '#backTop', function () {
  $('html, body').animate({scrollTop: 0}, 800);
  return false;
});


/********************** /END OF BACK TO TOP **********************/


/***************************** OTHER *****************************/

$('#pageForm').on('submit', function (event) {
  eventPreventDefault(event);
});

$('.datepicker-field').on('pick.datepicker', function () {
  $(this).focus();
});

$('.form-checkbox').on('change', function () {

  if(this.checked) {
    $(this).parents('.checkbox-wrapper').find('.form-checkbox-value').val(1);
  } else{
    $(this).parents('.checkbox-wrapper').find('.form-checkbox-value').val(0);
  }
});
