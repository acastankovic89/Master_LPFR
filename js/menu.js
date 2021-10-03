var RESPONSIVE_BREAKPOINT = 993;
var dropdownElementClass = '.dropdown';

// desktop
var $mainMenu = $('#mainMenu');
var $menuItem = $mainMenu.find('.dropdown-parent');
var $menuItemParentLink = $('.parent-link');

// mobile
var $menuOpener = $('#menuOpener');
var $dropdownOpener = $('.dropdown-opener');

var HIDE_FOR_DESKTOP;
var HIDE_FOR_MOBILE;

$(document).ready(
  function () {
    intitMenuVisibility();
  }
);

$(window).on('resize', function () {

  var windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

  if (windowWidth > RESPONSIVE_BREAKPOINT) {

    if (HIDE_FOR_MOBILE) {

      if ($menuItemParentLink.hasClass('active')) {
        $menuItemParentLink.removeClass('active');
        $(dropdownElementClass).hide();
      }

      HIDE_FOR_MOBILE = false;
      HIDE_FOR_DESKTOP = true;
    }

  } else {

    if (HIDE_FOR_DESKTOP) {

      if ($menuOpener.hasClass('active')) {
        $menuOpener.removeClass('active');
        $mainMenu.hide();
      }


      if ($dropdownOpener.hasClass('active')) {
        $dropdownOpener.removeClass('active');
        $(dropdownElementClass).hide();
      }

      HIDE_FOR_DESKTOP = false;
      HIDE_FOR_MOBILE = true;
    }
  }
});


function intitMenuVisibility() {

  var windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

  if (windowWidth > RESPONSIVE_BREAKPOINT) {
    HIDE_FOR_DESKTOP = false;
    HIDE_FOR_MOBILE = true;
  } else {
    HIDE_FOR_DESKTOP = true;
    HIDE_FOR_MOBILE = false;
  }
};


/***************************** DESKTOP *****************************/

$menuItem
  .on('mouseenter', function (event) {
    event.stopPropagation();
    openDropdown(this);
  })
  .on('mouseleave', function () {
    closeDropdown(this);
  });


function openDropdown(elem) {

  var windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

  if (windowWidth > RESPONSIVE_BREAKPOINT) {
    $(elem).find('a').first().addClass('hover');
    $(elem).find(dropdownElementClass).first().stop(true, true).slideDown().delay(300);
  }
};


function closeDropdown(elem) {

  var windowWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

  if (windowWidth > RESPONSIVE_BREAKPOINT) {
    $(elem).find(dropdownElementClass).first().slideUp();
    $(elem).find('a').first().removeClass('hover');
  }
};


/****************************** MOBILE ******************************/

$menuOpener.on('click', function () {
  $(this).toggleClass('active');
  $mainMenu.slideToggle();
});


$dropdownOpener.on('click', function (event) {
  eventPreventDefault(event);
  $(this).toggleClass('active');
  $(this).next('.dropdown').slideToggle();
});