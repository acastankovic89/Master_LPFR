$(document).ready(function () {
  setPageWrapper();
});

/******************************************* BACK TO TOP *******************************************/

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


/*************************************** /end of back to top ***************************************/


/********************************************** LOADER *********************************************/

$(window).on('load',
  function () {
    $('#loader').fadeOut();
    $('#overlay').fadeOut();
  }
);

/****************************************** /end of loader *****************************************/


/******************************************* PAGE WRAPPER ******************************************/

function setPageWrapper() {
  appendWrapperStart();
  wrapPage();
  setPageWrapperHeight();
};


function appendWrapperStart() {

  $('body').prepend('<div class="page-wrapper-start"></div>');

  if ($('.page-wrapper-start').length == 0) {

    setTimeout(function () {
      appendWrapperStart();
    }, 200);
  }
};


function wrapPage() {

  if ($('.page-wrapper-start').length != 0) $('.page-wrapper-start').nextUntil('footer').wrapAll('<div id="pageWrapper"></div>');

  if ($('#pageWrapper').length == 0) {

    setTimeout(function () {
      wrapPage();
    }, 200);
  }
};


function setPageWrapperHeight() {

  if ($('footer').length != 0) {

    var $footer = $('footer');

    var height = $('footer').height();
    var marginTop = parseFloat($footer.css('margin-top'));
    var marginBottom = parseFloat($footer.css('margin-top'));
    var paddingTop = parseFloat($footer.css('padding-top'));
    var paddingBottom = parseFloat($footer.css('padding-bottom'));

    var footerHeight = height + marginBottom + paddingTop + paddingBottom;

    $('#pageWrapper').attr('style', 'min-height: calc(100vh - ' + footerHeight + 'px)');
  }
};


/*************************************** /end of page wrapper **************************************/


/********************************************* LANGUAGES ********************************************/

$('.set-language').on('click', function (event) {
  eventPreventDefault(event);

  if ($(this).hasClass('active')) return;

  var langId = $(this).attr('data-id');
  var route = getUrlRoute();
  var payload = 'lang_id=' + langId;

  if ($('#langGroupId').length != 0) {
    var langGroupId = $('#langGroupId').val();
    payload += '&lang_group_id=' + langGroupId;
  }

  if (exists(route)) {
    payload += '&route=' + route;
  }

  axios.post(BASE_URL + '/languages-set/', payload, AXIOS_HEADERS)
    .then(function (response) {

      if(response.status == 200) {
        var url = response.data.data;
        location.href = url;
      }
    })
    .catch(function (error) {
      console.log(error);
    });
});

/**************************************** /end of languages ***************************************/


/*********************************************** SEARCH **********************************************/

$('#openSearchForm').on('click', function (event) {
  eventPreventDefault(event);

  $('#searchOverlay').fadeIn();
});


$('#closeSearchForm').on('click', function (event) {
  eventPreventDefault(event);

  $('#searchOverlay').fadeOut();
});

/****************************************** /end of search *****************************************/


/******************************************** NEWSLETTER *******************************************/

$('#newsletterForm').on('submit', function (event) {
  eventPreventDefault(event);

  var validated = validateRequiredFieldsWithWarningMessage('#newsletterForm');

  if (!validated) return;

  var payload = $(this).serialize();

  axios.post(API_URL + '/newsletter/signup', payload, AXIOS_HEADERS)
    .then(function (response) {

      if(response.status == 200) {

        var status = response.data.status;
        var message = response.data.message;

        alert(message);

        if (status == 0) {
          location.reload();
        }
      }
    })
    .catch(function (error) {
      console.log(error);
    });
});


/**************************************** /end of newsletter ***************************************/


/******************************************* NC SELECTBOX ******************************************/

$('.nc-selectbox-header').on('click', function (event) {
  eventPreventDefault(event);
  event.stopPropagation();

  var $parent = $(this).parents('.nc-selectbox');
  var $body = $parent.find('.nc-selectbox-body');

  $parent.toggleClass('active');

  if($parent.hasClass('active')) {

    $body.fadeIn();

  } else {

    $body.fadeOut();
  }
});

$('.nc-selectbox .nc-selectbox-body button').on('click', function (event) {
  eventPreventDefault(event);
  event.stopPropagation();

  var $parent = $(this).parents('.nc-selectbox');
  var $body = $parent.find('.nc-selectbox-body');

  var value = $(this).attr('data-value');
  var title = $(this).text();

  $parent.find('.nc-selectbox-value').val(value);
  $parent.find('.nc-selectbox-title').text(title).removeClass('placeholder');

  $parent.removeClass('active');
  $body.fadeOut();
});


$(document).on('click', function (event) {
  $('.nc-selectbox').removeClass('active');
  $('.nc-selectbox-body').fadeOut();
});

/*************************************** /end of nc selectbox **************************************/