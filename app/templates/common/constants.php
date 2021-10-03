<script>

  var BASE_URL = "<?php echo Conf::get('url'); ?>";
  var ADMIN_URL = "<?php echo Conf::get('admin_url'); ?>";

  var MEDIA_URL = "<?php echo Conf::get('media_url'); ?>";
  var MEDIA_THUMBS_URL = "<?php echo Conf::get('media_thumbs_url'); ?>";
  var CSS_IMG_URL = "<?php echo Conf::get('css_img_url'); ?>";

  var API_URL = "<?php echo Conf::get('api_url'); ?>";
  var CLIENT_ID = "<?php echo Conf::get('client_id'); ?>";
  var CLIENT_SECRET = "<?php echo Conf::get('client_secret'); ?>";

  var AXIOS_HEADERS = {
    headers: {
      // Authorization: 'Bearer ' + token //the token is a variable which holds the token
      'Accept': 'application/json',
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    isAxiosError: false
  };

  var ITEMS_PER_PAGE = {
    MODAL_IMAGES: "<?php echo Conf::get('items_per_page')['admin_modal_images']; ?>"
  };

  var MODAL_TYPE = {
    INTRO_IMAGE: "<?php echo Conf::get('modal_type')['intro_image']; ?>",
    MAIN_IMAGE: "<?php echo Conf::get('modal_type')['main_image']; ?>",
    TEXT_EDITOR_IMAGE: "<?php echo Conf::get('modal_type')['text_editor_image']; ?>",
    GALLERY_IMAGE: "<?php echo Conf::get('modal_type')['gallery_image']; ?>",
    GALLERY_YOUTUBE_VIDEO: "<?php echo Conf::get('modal_type')['gallery_youtube_video']; ?>",
    DOCUMENT: "<?php echo Conf::get('modal_type')['document']; ?>",
    CONTENT_VIDEO: "<?php echo Conf::get('modal_type')['content_video']; ?>",
    ALL: "<?php echo Conf::get('modal_type')['all']; ?>"
  };

  var SITE_LANG = "<?php echo Trans::getLanguageAlias(); ?>";

  var MENU_ITEM_TYPE_ID = {
    ARTICLE: 1,
    CATEGORY: 2,
    EXTERNAL_LINK: 3,
    SEPARATOR: 4
  };


  var NC_GALLERY_LABEL = "<?php echo Conf::get('nc_gallery_label'); ?>";

  Object.freeze(ITEMS_PER_PAGE);
  Object.freeze(MODAL_TYPE);
  Object.freeze(MENU_ITEM_TYPE_ID);

</script>