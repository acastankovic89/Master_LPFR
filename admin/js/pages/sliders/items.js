$(document).ready(function () {
  if (domElemExists('#sliderItemsInsertPage')) {
    initSliderItemsTree();
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

        asyncDeleteAction('/slider_items/delete/' + id, AXIOS_HEADERS);

      } else {
        swal('Deletion canceled.');
      }
    });
});