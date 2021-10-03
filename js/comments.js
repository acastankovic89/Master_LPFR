$(document).on('submit', '.add-comment-form', function (event) {
  eventPreventDefault(event);

  var validateRequired = validateRequiredFieldsWithWarningMessage(this);

  if (!validateRequired) {
    return;
  }

  var type = $(this).attr('data-type');
  var payload = $(this).serialize();

  var self = this;

  axios.post(API_URL + '/comments/insert', payload, AXIOS_HEADERS)
    .then(function (response) {

      if(response.status == 200) {

        var status = response.data.status;
        var message = response.data.message;

        alert(message);

        if (status == 0) {


          if (type == 'reply') {

            $(self).parents('.reply-form-wrapper').remove();

          } else if (type == 'comment') {

            $(self).find('.form-field').val('');
          }
        }
      }

    })
    .catch(function (error) {
      console.log(error);
    });
});


$('.comment-reply').on('click', function (event) {
  eventPreventDefault(event);

  var targetId = $(this).attr('data-target_id');
  var parentId = $(this).attr('data-parent_id');
  var typeId = $(this).attr('data-type_id');

  $('.reply-form-wrapper').remove();

  var replyForm = renderReplyForm(targetId, parentId, typeId);

  $(this).parents('.comment-reply-wrapper').after(replyForm);
});


$(document).on('click', '.close-reply', function (event) {
  eventPreventDefault(event);

  $('.reply-form-wrapper').remove();
});


function renderReplyForm(targetId, parentId, typeId) {

  var html = '<div class="comments-form-wrapper reply-form-wrapper">';

    html += '<form class="add-comment-form" data-type="reply">';

      html += '<div class="close-reply-wrapper"><button type="button" class="close-reply"><i class="fa fa-times"></i></div>';
      html += '<input type="hidden" name="target_id" class="comment-target_id" value="' + targetId + '" />';
      html += '<input type="hidden" name="parent_id" class="comment-parent_id" value="' + parentId + '" />';
      html += '<input type="hidden" name="type_id" class="comment-type_id" value="' + typeId + '" />';
      html += '<label>Name:</label>';
      html += '<input type="text" name="name" class="form-field required comment-name" required="required" />';
      html += '<label>E-mail:</label>';
      html += '<input type="email" name="email" class="form-field required comment-email" required="required" />';
      html += '<label>Message:</label>';
      html += '<textarea name="message" class="form-field required comment-message" required="required"></textarea>';
      html += '<div class="post-button-wrapper">';
        html += '<button type="submit">Post comment</button>';
      html += '</div>';

    html += '</form>';

  html += '</div>';

  return html;
};