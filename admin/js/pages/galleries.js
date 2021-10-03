// insert/update item
$('.page-form').on('submit', function (event) {
  eventPreventDefault(event);

  var requiredFieldsValidated = validateRequiredFields(this);
  if (!requiredFieldsValidated) return;

  disableCtaButtons();

  var langSuffix = $(this).data('lang_suffix');

  var gallery = setGallery(langSuffix);
  $(this).find('.item-gallery_json').val(JSON.stringify(gallery));

  var id = $(this).find('.item-id').val();
;
  var payload = $(this).serialize();

  if (id == 0) {

    asyncInsertAction('/galleries/insert', payload, AXIOS_HEADERS, '/administration/galleries/:id/insert');

  } else {

    asyncUpdateAction('/galleries/update', payload, AXIOS_HEADERS);
  }
});


// delete item
$('.delete').on('click', function (event) {
  eventPreventDefault(event);

  var id = $(this).attr('data-id');

  swal({
    title: 'Are you sure?',
    text: 'You are about to delete this item!',
    icon: 'warning',
    buttons: true,
    dangerMode: true,
  })
    .then((willDelete) => {
      if (willDelete) {

        asyncDeleteAction('/galleries/delete/' + id, AXIOS_HEADERS);

      } else {
        swal('Deletion canceled.');
      }
    });
});