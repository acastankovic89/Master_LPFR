// publish
$(document).on('click', '.comment-publish', function (event) {
  eventPreventDefault(event);

  var id = $(this).data('id');
  var published = $(this).attr('data-published');

  var payload = 'id=' + id + '&published=' + published;

  var self = this;
  axios.put(API_URL + '/comments/publish', payload, AXIOS_HEADERS)
    .then(function (response) {

      var status = response.data.status;
      var published = response.data.published;
      var message = response.data.message;
      var buttonText = response.data.buttonText;

      if(status == 0) {
        swal('Success', message, 'success')
          .then(() => {
            $(self).attr('data-published', published);
            $(self).text(buttonText);
          });
      }

    })
    .catch(function (error) {
      console.log(error);
    });
});


// delete
$('.comment .comment-delete').on('click', function (event) {
  eventPreventDefault(event);

  var id = $(this).data('id');
  //var parentId = $(this).data('parent_id');
  var $comment = $(this).parent('.comment-header').parent('.comment');

  swal({
    title: 'Are you sure?',
    text: 'You are about to delete this item!',
    icon: 'warning',
    buttons: true,
    dangerMode: true,
  })
    .then((willDelete) => {
      if (willDelete) {

        axios.delete(API_URL + '/comments/delete/' + id, AXIOS_HEADERS)
          .then(function (response) {

            var status = response.data.status;
            var message = response.data.message;

            if (status == 0) {

              swal('Success', message, 'success')
                .then(() => {
                  $comment.remove();
                });
            } else {
              swal('Warning', message, 'warning');
            }
          })
          .catch(function (error) {
            console.log(error);
          });

      } else {
        swal('Deletion canceled.');
      }
    });
});