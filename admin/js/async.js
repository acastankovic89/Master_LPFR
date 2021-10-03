function asyncInsertAction(url, payload, headers, redirectUrl = null) {

  axios.post(API_URL + url, payload, headers)
    .then(function (response) {

      var status = response.data.status;
      var message = response.data.message;

      if (status == 0) {

        var id = response.data.lastInsertId;

        if(exists(redirectUrl)) {
          redirectUrl = redirectUrl.replace(':id', id);
        }

        swal('Success', message, 'success')
          .then(() => {

            if(exists(redirectUrl)) {
              location.href = BASE_URL + redirectUrl;
            } else {
              location.reload();
            }

          });
      } else {
        swal('Warning', message, 'warning');
      }
    })
    .catch(function (error) {
      console.log(error);
    })
    .finally(function () {
      enableCtaButtons();
    });
};


function asyncUpdateAction(url, payload, headers) {

  axios.put(API_URL + url, payload, headers)
    .then(function (response) {

      var status = response.data.status;
      var message = response.data.message;

      if (status == 0) {

        swal('Success', message, 'success')
          .then(() => {
            location.reload();
          });
      } else {
        swal('Warning', message, 'warning');
      }
    })
    .catch(function (error) {
      console.log(error);
    })
    .finally(function () {
      enableCtaButtons();
    });
};


function asyncDeleteAction(url, headers) {

  axios.delete(API_URL + url, headers)
    .then(function (response) {

      var status = response.data.status;
      var message = response.data.message;

      if (status == 0) {

        swal('Success', message, 'success')
          .then(() => {
            location.reload();
          });
      } else {
        swal('Warning', message, 'warning');
      }
    })
    .catch(function (error) {
      console.log(error);
    })
    .finally(function () {
      enableCtaButtons();
    });
}