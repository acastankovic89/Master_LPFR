<?php

  Conf::set('normacore_version', '3.0.0');

  if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    Conf::set('root', str_replace('\config\params\sys', '',dirname(__FILE__))); // Windows
  } else {
    Conf::set('root', str_replace('/config/params/sys', '',dirname(__FILE__)));
  }

  Conf::set('log', Conf::get('root') . '/log/log.txt');
  Conf::set('log_trace', Conf::get('root') . '/log/log_trace.txt');
  Conf::set('display_error', false);
  Conf::set('debug', false);


  //* Encryption parameters *//

  Conf::set('enc_hash', 'wO6sfuXa4vCNH4ZnGaX5');
  Conf::set('enc_type', 'sha256');


  //* Users module parameters *//

  Conf::set('session_php', true);
  Conf::set('session_token', true);


  //set time zone
  date_default_timezone_set('europe/berlin');

  Conf::set('modules', array());

  // media files
  Conf::set('media_root', Conf::get('root') . '/media');
  Conf::set('media_url', Conf::get('url') . '/media');

  Conf::set('media_thumbs_root', Conf::get('media_root') . '/thumbs');
  Conf::set('media_thumbs_url', Conf::get('media_url') . '/thumbs');

  // images from css dir
  Conf::set('css_img_root', Conf::get('root') . '/css/img');
  Conf::set('css_img_url', Conf::get('url') . '/css/img');


  //* User roles *//

  Conf::set('user_role_id', array(
    'admin' => 1,
    'editor' => 2
  ));


  //* Admin modal types *//

  Conf::set('modal_type', array(
    'intro_image' => 1,
    'main_image' => 2,
    'text_editor_image' => 3,
    'gallery_image' => 4,
    'gallery_youtube_video' => 5,
    'document' => 6,
    'content_video' => 7,
    'all' => 8
  ));


  //* Media upload statuses *//

  Conf::set('media_upload_status', array(
    'success' => 1,
    'already_uploaded' => 2,
    'failed' => 3
  ));


  //* Comment types *//

  Conf::set('comment_type_id', array(
    'article' => 1,
    'category' => 2,
    'product' => 3,
  ));


  Conf::set('nc_gallery_label', 'gallery_id');


  Conf::set('items_per_row_table_filter', array("10", "25", "50", "100"));

?>