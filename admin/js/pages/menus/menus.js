var $formModal = $('#formModal');

function openFormModal() {
  $formModal.parents('.modal-overlay').fadeIn();
  $formModal.addClass('show');
};

function emptyFormModalFields() {
  $formModal.find('.item-id').val(0);
  $formModal.find('.item-lang_group_id').val(0);
  $formModal.find('.item-name').val('').parents('.field-wrapper').removeClass('focus');
  $formModal.find('.field-wrapper').removeClass('warning');
};

function setActiveLangTab(langId) {

  $('.lang-btn').removeClass('active');

  if(!exists(langId) || langId == 0) {
    $('.lang-btn').eq(0).addClass('active');
    return;
  }

  $('.lang-btn').each(function () {

    var id = $(this).attr('data-id');

    if(langId == id) {
      $(this).addClass('active');
      return;
    }
  });
};

function setActiveForm(langId) {

  $('.page-form').removeClass('active');

  if(!exists(langId) || langId == 0) {
    $('.page-form').eq(0).addClass('active');
    return;
  }

  $('#pageForm-langId-' + langId).addClass('active');
};

function getActiveLangId() {
  var id = 0;
  $('.lang-btn').each(function () {

    if($(this).hasClass('active')) {
      id = $(this).attr('data-id');
    }
  });
  return id;
};

$('.edit, .add-new-item').on('click', function (event) {
  eventPreventDefault(event);

  disablePageCtaButtons();

  var id = $(this).data('id');
  var langId = $(this).data('lang_id');

  setActiveLangTab(langId);
  setActiveForm(langId);

  var headers = {
    headers: {
      'Content-Type': 'application/json;charset=UTF-8',
      'Accept': 'application/json'
    },
    isAxiosError: false
  };

  axios.get(API_URL + '/menus/group/' + id, headers)
    .then(function (response) {

      if(response.status == 200) {
        return response.data.data;
      }
    })
    .then(function (data) {
      var items = data.items;
      var langGroupId = data.langGroupId;

      if(!exists(langGroupId)) {
        langGroupId = 0;
      }

      for(var key in items) {

        var item = items[key];

        $('#itemId-langId-' + item.lang_id).val(item.id);
        $('#itemLangGroupId-langId-' + item.lang_id).val(langGroupId);
        if(exists(item.name)) {
          $('#itemName-langId-' + item.lang_id).val(item.name).parents('.field-wrapper').addClass('focus');
        }
      }

      openFormModal();
    })
    .catch(function (error) {
      console.log(error);
    });

});

$formModal.find('.modal-close, .modal-cancel').on('click', function (event) {
  eventPreventDefault(event);
  emptyFormModalFields();
  enablePageCtaButtons();
});

$formModal.find('.modal-save').on('click', function (event) {
  eventPreventDefault(event);

  var langId = getActiveLangId();

  var $form = $('#pageForm-langId-' + langId);

  var requiredFieldsValidated = validateRequiredFields($form);
  if (!requiredFieldsValidated) return;

  disableCtaButtons();

  var id = $form.find('.item-id').val();
  var payload = $form.serialize();

  if(id == 0) {

    asyncInsertAction('/menus/insert', payload, AXIOS_HEADERS);
    enablePageCtaButtons();
  } else {

    asyncUpdateAction('/menus/update', payload, AXIOS_HEADERS);
    enablePageCtaButtons();
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

        asyncDeleteAction('/menus/delete/' + id, AXIOS_HEADERS);

      } else {
        swal('Deletion canceled.');
      }
    });
});