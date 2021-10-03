$('#contactForm').on('submit', function (event) {
  eventPreventDefault(event);

  var validated = validateRequiredFieldsWithWarningMessage(this);

  if (!validated) return;

  var payload = $(this).serialize();

  axios.post(BASE_URL + '/send-email', payload, AXIOS_HEADERS)
    .then(function (response) {

      var success = response.data.success;
      var message = response.data.message;

      alert(message);

      if (success) {
        location.reload();
      }
    })
    .catch(function (error) {
      console.log(error);
    });
});

$('.form-btn-clear').on('click', function (event) {
  eventPreventDefault(event);

  clearFormFields(this);
});



