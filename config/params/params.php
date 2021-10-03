<?php


    Conf::set('base', 'lpfr_site'); // project directory name (on production it should be empty string)


    Conf::set('api', 'api/v1');
    Conf::set('client_id', 'normacore3');
    Conf::set('client_secret', 'gwj3#6N4fGK,8ue$'); // 16 digit secret


    //* Application parameters *//
    Conf::set('site_name', 'Master LPFR');
    Conf::set('session_prefix', 'normacore3'); // used for login sessions, etc.


    // URL constants are stored in set_url_params.php
    if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      require_once(str_replace('\config\params', '', dirname(__FILE__)) . '\core\libs\set_url_params.php'); // Windows
    } else {
      require_once(str_replace('/config/params', '', dirname(__FILE__)) . '/core/libs/set_url_params.php');
    }


    Conf::set('api_url', Conf::get('url') . '/' . Conf::get('api'));

    //* Database parameters *//

    Conf::set('db_hostname', 'webdev.normasoft.net');
    Conf::set('db_username', 'root');
    Conf::set('db_password', 'pr3ko7po8bg');
    Conf::set('db_name', 'lpfr_site');
    
    //* Oauth2 parameters *//
    Conf::set('Oauth2_access_lifetime', 8640000);
    Conf::set('Oauth2_refresh_token_lifetime', 864000);


    Conf::set('activation_token_lifetime', 86400);
    Conf::set('reset_password_token_lifetime', 86400);


    //* Pagination parameters *//

    Conf::set('pagination_exists', array(
              'articles'   => false,
              'categories' => false,
              'products'   => false)
    );

    Conf::set('items_per_page', array(
              'site_articles'      => 8,
              'site_categories'    => 10,
              'site_products'      => 12,
              'admin_modal_images' => 20,
              'admin_table'        => 10
    ));


    //* Permissions parameters *//

    Conf::set('multilingual_enabled', true);


    //* Meta parameters *//

    Conf::set('meta_tags', array(
      'title' => Conf::get('site_name'),
      'description' => '',
      'keywords' => '',
      'og' => array(
        'title' => Conf::get('site_name'),
        'image' => Conf::get('url') . '/css/img/logo.png',
        'width' => 1200,
        'height' => 630,
        'url' => Conf::get('url'),
        'site_name' => 'normacore.com'
      )
    ));


    //* Email parameters *//

    Conf::set('mail_from_address', 'aleksandar.stankovic@normasoft.net');
    Conf::set('mail_from_name', 'Normacore');
    Conf::set('mail_to_address', 'aleksandar.stankovic@normasoft.net');    // recipient address


    //* Default language parameters *//

    Conf::set('language_default', array(
      'id'    => 1,
      'name'  => 'serbian',
      'alias' => 'sr'
    ));


    //* Category parameters *//


    //* Article parameters *//


    //* Menu parameters *//

    Conf::set('main_menu_id', 1);


    //* Slider parameters *//

    Conf::set('home_slider_id', 1);
?>