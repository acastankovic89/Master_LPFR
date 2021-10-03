var $formModal = $('#formModal');

function openFormModal() {
  $formModal.parents('.modal-overlay').fadeIn();
  $formModal.addClass('show');
};

function hideModalFields() {
  $('#menuItemsInsertPage .categories-options').hide().removeClass('focus');
  $('#menuItemsInsertPage .categories-options .nc-select-value').removeClass('required');
  $('#menuItemsInsertPage .articles-options').hide().removeClass('focus');
  $('#menuItemsInsertPage .articles-options .nc-select-value').removeClass('required');
  $('#menuItemsInsertPage .url-field').hide().removeClass('focus');
  $('#menuItemsInsertPage .url-field .form-field').removeClass('required');
};

function showCategorySelect() {
  $('#menuItemsInsertPage .categories-options').fadeIn();
  $('#menuItemsInsertPage .categories-options .nc-select-value').addClass('required');
};

function showArticleSelect() {
  $('#menuItemsInsertPage .articles-options').fadeIn();
  $('#menuItemsInsertPage .articles-options .nc-select-value').addClass('required');
};

function showUrlField() {
  $('#menuItemsInsertPage .url-field').fadeIn().addClass('focus');
  $('#menuItemsInsertPage .url-field .form-field').addClass('required');
};

function setSelectboxValue(selectvalue, elemName, hiddenValue) {

  $(elemName).find('.nc-select-options a').each(function () {

    var value = $(this).attr('data-value');
    var text = $(this).text();

    if(value == hiddenValue) {
      $(this).hide();
    }

    if(value == selectvalue) {

      $(this).parents('.nc-selectbox').find('.nc-select').val(text);
      $(this).parents('.nc-selectbox').find('.nc-select-value').val(value);
      $(this).parents('.field-wrapper').addClass('focus');
    }
  });
};

function clearFromModalSelectboxes() {
  $formModal.find('.nc-selectbox .nc-select').val('');
  $formModal.find('.nc-selectbox .nc-select-value').val('');
  $formModal.find('.nc-selectbox').parents('.field-wrapper').removeClass('focus');
}

function emptyFormModalFields() {
  $('#formModal [name="id"]').val(0);
  $('#formModal [name="name"]').val('').parents('.field-wrapper').removeClass('focus');
  $('#formModal [name="parent_id"]').val('');
  $('#formModal [name="menu_id"]').val('');
  $('#formModal [name="target_id"]').val('');
  $('#formModal [name="type"]').val('');
  clearFromModalSelectboxes();
  $('.nc-select-options a').show();
};

$('.add-new-item').on('click', function (event) {
  eventPreventDefault(event);
  openFormModal();
});

$('.edit').on('click', function (event) {
  eventPreventDefault(event);

  var id = $(this).attr('data-id');
  var name = $(this).attr('data-name');
  var parentId = $(this).attr('data-parent_id');
  var menuId = $(this).attr('data-menu_id');
  var targetId = $(this).attr('data-target_id');
  var type = $(this).attr('data-type');
  var url = $(this).attr('data-url');

  $('#formModal [name="id"]').val(id);
  $('#formModal [name="name"]').val(name).parents('.field-wrapper').addClass('focus');
  $('#formModal [name="parent_id"]').val(parentId);
  $('#formModal [name="menu_id"]').val(menuId);
  $('#formModal [name="target_id"]').val(targetId);
  $('#formModal [name="type"]').val(type);

  setSelectboxValue(parentId, '#itemParentId', id);
  setSelectboxValue(type, '#itemTypeId');

  hideModalFields();
  if(type == MENU_ITEM_TYPE_ID.ARTICLE) {
    setSelectboxValue(targetId, '#itemArticleSelect');
    showArticleSelect();
  }else if(type == MENU_ITEM_TYPE_ID.CATEGORY) {
    setSelectboxValue(targetId, '#itemCategorySelect');
    showCategorySelect();
  }else if(type == MENU_ITEM_TYPE_ID.EXTERNAL_LINK) {
    $('#itemUrl').val(url);
    showUrlField();
  }

  openFormModal();
});

$('#itemTypeId .nc-select-options a').on('click', function (event) {
  eventPreventDefault(event);

  var value = $(this).data('value');

  hideModalFields();
  if (value == MENU_ITEM_TYPE_ID.ARTICLE) showArticleSelect();
  else if (value == MENU_ITEM_TYPE_ID.CATEGORY) showCategorySelect();
  else if (value == MENU_ITEM_TYPE_ID.EXTERNAL_LINK) showUrlField();
});

$('#itemCategorySelect .nc-select-options a').on('click', function (event) {
  eventPreventDefault(event);

  var value = $(this).data('value');
  $('#itemTargetId').val(value);
});

$('#itemArticleSelect .nc-select-options a').on('click', function (event) {
  eventPreventDefault(event);

  var value = $(this).data('value');
  $('#itemTargetId').val(value);
});


$(document).ready(function () {
  if (domElemExists('#menuItemsInsertPage')) {
    initMenuItemsTree();
  }
});

$formModal.find('.modal-close, .modal-cancel').on('click', function (event) {
  eventPreventDefault(event);
  emptyFormModalFields();
});

$formModal.find('.modal-save').on('click', function (event) {
  eventPreventDefault(event);

  var requiredFieldsValidated = validateRequiredFields('#menuItemForm');
  if (!requiredFieldsValidated) return;

  disableCtaButtons();

  var id = $('#formModal [name="id"]').val();
  var payload = $('#menuItemForm').serialize();

  if(id == 0) {

    asyncInsertAction('/menu_items/insert', payload, AXIOS_HEADERS);

  } else {

    asyncUpdateAction('/menu_items/update', payload, AXIOS_HEADERS);
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

        asyncDeleteAction('/menu_items/delete/' + id, AXIOS_HEADERS);

      } else {
        swal('Deletion canceled.');
      }
    });
});