// insert/update item
$('.page-form').on('submit', function (event) {
  eventPreventDefault(event);

  var requiredFieldsValidated = validateRequiredFields(this);
  if (!requiredFieldsValidated) return;

  disableCtaButtons();

  var id = $(this).find('.item-id').val();
  var sliderId = $(this).find('.item-slider_id').val();
  var payload = $(this).serialize();

  if (id == 0) {

    var $redirectUrl = '/administration/sliders/' + sliderId + '/slider_items/:id/insert';

    asyncInsertAction('/slider_items/insert', payload, AXIOS_HEADERS, $redirectUrl);

  } else {

    asyncUpdateAction('/slider_items/update', payload, AXIOS_HEADERS);
  }
});