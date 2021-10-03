var $loginButton = $('#loginButton');

function disableLoginButton() {
  $loginButton.addClass('disabled');
  $loginButton.attr('disabled', 'disabled');
};


function enableLoginButton() {
  $loginButton.removeClass('disabled');
  $loginButton.removeAttr('disabled');
};


$('#loginForm').on('submit', function (event) {
  event.preventDefault();

  disableLoginButton();

  var payload = new FormData(document.getElementById('loginForm'));
  payload.append('grant_type', 'password');
  payload.append('client_id', CLIENT_ID);
  payload.append('client_secret', CLIENT_SECRET);
  console.log(API_URL);
  console.log(API_URL + '/auth/login');
  axios.post(API_URL + '/auth/login', payload, AXIOS_HEADERS)
    .then(function (response) {
      if (exists(response.data.access_token)) {
        location.href = BASE_URL + "/administration/dashboard";
      } else if (exists(response.data.message)) {
        swal("Upozorenje", response.data.message, "warning")
          .then(function () {
              enableLoginButton();
            }
          );
      } else {
        swal("Upozorenje", "Server error", "warning")
          .then(function () {
              enableLoginButton();
            }
          );
      }
    })
    .catch(function (error) {
      console.log(error);
    });
});