$(document).ready(function () {
  initDropZone();
});

var ncDropzone;

function initDropZone() {

  ncDropzone = new Dropzone('#ncDropzone',
    {
      url: API_URL + '/media/upload',
      maxFiles: 12, // Maximum Number of Files
      maxFilesize: 200,
      dictDefaultMessage: 'Drop files here to upload (or click).'
    }
  );

  ncDropzone.on('error', function (file, response) {
    console.log(file);
    console.log(response);
  })
  .on('queuecomplete', function (response) {

    setTimeout(
      function () {

        location.reload();
      }, 800
    );
  });
};

// delete item
$('.delete').on('click', function (event) {
  event.preventDefault();

  var id = $(this).attr('data-id');

  swal({
    title: "Are you sure?",
    text: "You are about to delete this item!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {

      asyncDeleteAction('/media/delete/' + id, AXIOS_HEADERS);

    } else {
      swal("Deletion canceled.");
    }
  });
});