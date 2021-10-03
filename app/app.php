<?php

class NormacoreApplication extends Application {

  protected $name = 'normacore3';

  protected $resources = array();

  protected $default = array(
    'route'      => ':alias',
    'method'     => 'get',
    'controller' => 'pages',
    'action'     => 'aliasDecoding'
  );

//    protected $resources = array (
//        ['name' => 'users', 'type' => ResourceTypes::API]
//    );

  protected $routes = array(


    /************** API **************/


    /* authorization */

    array('route'      => 'api/v1/auth/token',
          'method'     => 'post',
          'controller' => 'auth',
          'action'     => 'token'),

    array('route'      => 'api/v1/auth/login',
          'method'     => 'post',
          'controller' => 'auth',
          'action'     => 'login'),

    array('route'      => 'api/v1/auth/revoke',
          'method'     => 'post',
          'controller' => 'auth',
          'action'     => 'revoke'),

    array('route'      => 'api/v1/auth/logout',
          'method'     => 'post',
          'controller' => 'auth',
          'action'     => 'logout'),


    /*** users ***/

    array('route'      => 'api/v1/users/register',
          'method'     => 'post',
          'controller' => 'users',
          'action'     => 'register'),

    // activate user after registration (by link)
    array('route'      => 'users/activate/:activation_token',
          'method'     => 'get',
          'controller' => 'users',
          'action'     => 'activate'),

    // activate user after registration (by form insert)
    array('route'      => 'api/v1/users/activate',
          'method'     => 'post',
          'controller' => 'users',
          'action'     => 'activate'),

    array('route'      => 'api/v1/users/send-reset-password',
          'method'     => 'post',
          'controller' => 'users',
          'action'     => 'sendResetPassword'),

    array('route'      => 'api/v1/users/reset-password',
          'method'     => 'post',
          'controller' => 'users',
          'action'     => 'resetPassword'),

    array('route'      => 'api/v1/users/:id',
          'method'     => 'get',
          'controller' => 'users',
          'action'     => 'fetchOne'),

    array('route'      => 'api/v1/users',
          'method'     => 'get',
          'controller' => 'users',
          'action'     => 'fetchAll'),

    array('route'      => 'api/v1/users/insert',
          'method'     => 'post',
          'controller' => 'users',
          'action'     => 'insertUser'),

    array('route'      => 'api/v1/users/update',
          'method'     => 'put',
          'controller' => 'users',
          'action'     => 'updateUser'),

    array('route'      => 'api/v1/users/delete/:id',
          'method'     => 'delete',
          'controller' => 'users',
          'action'     => 'deleteUser'),

      array('route'      => 'api/v1/users/change-password',
            'method'     => 'put',
            'controller' => 'users',
            'action'     => 'changePassword'),


    /*** categories ***/

    array('route'      => 'api/v1/categories/:id',
          'method'     => 'get',
          'controller' => 'categories',
          'action'     => 'fetchOne'),

    array('route'      => 'api/v1/categories',
          'method'     => 'get',
          'controller' => 'categories',
          'action'     => 'fetchAll'),

    array('route'      => 'api/v1/categories/insert',
          'method'     => 'post',
          'controller' => 'categories',
          'action'     => 'insertCategory'),

    array('route'      => 'api/v1/categories/update',
          'method'     => 'put',
          'controller' => 'categories',
          'action'     => 'updateCategory'),

    array('route'      => 'api/v1/categories/delete/:id',
          'method'     => 'delete',
          'controller' => 'categories',
          'action'     => 'deleteCategory'),


    /*** articles ***/

    array('route'      => 'api/v1/articles/:id',
          'method'     => 'get',
          'controller' => 'articles',
          'action'     => 'fetchOne'),

    array('route'      => 'api/v1/articles',
          'method'     => 'get',
          'controller' => 'articles',
          'action'     => 'fetchAllAPI'),

    array('route'      => 'api/v1/articles/insert',
          'method'     => 'post',
          'controller' => 'articles',
          'action'     => 'insertArticle'),

    array('route'      => 'api/v1/articles/update',
          'method'     => 'put',
          'controller' => 'articles',
          'action'     => 'updateArticle'),

    array('route'      => 'api/v1/articles/delete/:id',
          'method'     => 'delete',
          'controller' => 'articles',
          'action'     => 'deleteArticle'),


    /*** media ***/

    array('route'      => 'api/v1/media/fetch-with-filters',
          'method'     => 'post',
          'controller' => 'media',
          'action'     => 'fetchWithFilters'),

    array('route'      => 'api/v1/media/upload',
          'method'     => 'post',
          'controller' => 'media',
          'action'     => 'uploadMedia'),

    array('route'      => 'api/v1/media/delete/:id',
          'method'     => 'delete',
          'controller' => 'media',
          'action'     => 'deleteMedia'),


    /*** menus ***/

    array('route'      => 'api/v1/menus/insert',
          'method'     => 'post',
          'controller' => 'menus',
          'action'     => 'insertMenu'),

    array('route'      => 'api/v1/menus/update',
          'method'     => 'put',
          'controller' => 'menus',
          'action'     => 'updateMenu'),

    array('route'      => 'api/v1/menus/delete/:id',
          'method'     => 'delete',
          'controller' => 'menus',
          'action'     => 'deleteMenu'),

    array('route'      => 'api/v1/menus/group/:id',
          'method'     => 'get',
          'controller' => 'menus',
          'action'     => 'fetchGroup'),


    /*** menu items ***/

    array('route'      => 'api/v1/menu_items/insert',
          'method'     => 'post',
          'controller' => 'menus',
          'action'     => 'insertMenuItem'),

    array('route'      => 'api/v1/menu_items/update',
          'method'     => 'put',
          'controller' => 'menus',
          'action'     => 'updateMenuItem'),

    array('route'      => 'api/v1/menu_items/delete/:id',
          'method'     => 'delete',
          'controller' => 'menus',
          'action'     => 'deleteMenuItem'),

    array('route'      => 'api/v1/menu_items/position',
          'method'     => 'put',
          'controller' => 'menus',
          'action'     => 'updateItemsPosition'),


    /*** sliders ***/

    array('route'      => 'api/v1/sliders/insert',
          'method'     => 'post',
          'controller' => 'sliders',
          'action'     => 'insertSlider'),

    array('route'      => 'api/v1/sliders/update',
          'method'     => 'put',
          'controller' => 'sliders',
          'action'     => 'updateSlider'),

    array('route'      => 'api/v1/sliders/delete/:id',
          'method'     => 'delete',
          'controller' => 'sliders',
          'action'     => 'deleteSlider'),

    array('route'      => 'api/v1/sliders/group/:id',
          'method'     => 'get',
          'controller' => 'sliders',
          'action'     => 'fetchGroup'),


    /*** slider items ***/

    array('route'      => 'api/v1/slider_items/insert',
          'method'     => 'post',
          'controller' => 'sliders',
          'action'     => 'insertSliderItem'),

    array('route'      => 'api/v1/slider_items/update',
          'method'     => 'put',
          'controller' => 'sliders',
          'action'     => 'updateSliderItem'),

    array('route'      => 'api/v1/slider_items/delete/:id',
          'method'     => 'delete',
          'controller' => 'sliders',
          'action'     => 'deleteSliderItem'),

    array('route'      => 'api/v1/slider_items/position',
          'method'     => 'put',
          'controller' => 'sliders',
          'action'     => 'updateItemsPosition'),


    /*** galleries ***/

    array('route'      => 'api/v1/galleries/insert',
          'method'     => 'post',
          'controller' => 'galleries',
          'action'     => 'insertGallery'),

    array('route'      => 'api/v1/galleries/update',
          'method'     => 'put',
          'controller' => 'galleries',
          'action'     => 'updateGallery'),

    array('route'      => 'api/v1/galleries/delete/:id',
          'method'     => 'delete',
          'controller' => 'galleries',
          'action'     => 'deleteGallery'),


    /*** comments ***/

    array('route'      => 'api/v1/comments/insert',
          'method'     => 'post',
          'controller' => 'comments',
          'action'     => 'insertComment'),

    array('route'      => 'api/v1/comments/publish',
          'method'     => 'put',
          'controller' => 'comments',
          'action'     => 'publishComment'),

    array('route'      => 'api/v1/comments/delete/:id',
          'method'     => 'delete',
          'controller' => 'comments',
          'action'     => 'deleteComment'),


    /*** newsletter ***/

    array('route'      => 'api/v1/newsletter/signup',
          'method'     => 'post',
          'controller' => 'newsletter',
          'action'     => 'newsletterSignup'),


    /********** ADMIN PAGES (static) **********/

    /*** login ***/

    array('route'      => 'administration',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'loginPage'),

    /*** dashboard ***/

    array('route'      => 'administration/dashboard',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'dashboardPage'),

    /*** users ***/

    array('route'      => 'administration/users',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'usersPage'),

    array('route'      => 'administration/users/:id/insert',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'userInsertPage'),

    /*** categories ***/

    array('route'      => 'administration/categories',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'categoriesPage'),

    array('route'      => 'administration/categories/:id/insert',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'categoryInsertPage'),

    /*** articles ***/

    array('route'      => 'administration/articles',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'articlesPage'),

    array('route'      => 'administration/articles/:id/insert',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'articleInsertPage'),

    /*** media ***/

    array('route'      => 'administration/media',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'mediaPage'),

    /*** menus ***/

    array('route'      => 'administration/menus',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'menusPage'),

    array('route'      => 'administration/menus/:id/insert',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'menuItemsPage'),

    /*** sliders ***/

    array('route'      => 'administration/sliders',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'slidersPage'),

    array('route'      => 'administration/sliders/:id/insert',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'sliderItemsPage'),

    array('route'      => 'administration/sliders/:slider_id/slider_items/:id/insert',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'sliderItemInsertPage'),

    /*** galleries ***/

    array('route'      => 'administration/galleries',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'galleriesPage'),

    array('route'      => 'administration/galleries/:id/insert',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'galleryInsertPage'),

    /*** newsletter ***/

    array('route'      => 'administration/newsletter',
          'method'     => 'get',
          'controller' => 'admin',
          'action'     => 'newsletterPage'),

    array('route'      => 'administration/newsletter/download',
          'method'     => 'get',
          'controller' => 'newsletter',
          'action'     => 'download'),

    /********** WEBSITE PAGES (static) **********/

    array('route'      => '',
          'method'     => 'get',
          'controller' => 'pages',
          'action'     => 'homePage'),

    array('route'      => 'kontakt',
          'method'     => 'get',
          'controller' => 'pages',
          'action'     => 'contactPage',
          'langAlias'  => 'sr'),

    array('route'      => 'contact',
          'method'     => 'get',
          'controller' => 'pages',
          'action'     => 'contactPage',
          'langAlias'  => 'en'),

    /************* ASYNC *************/

    array('route'      => 'send-email',
          'method'     => 'post',
          'controller' => 'async',
          'action'     => 'sendEmail'),

    array('route'      => 'languages-set',
          'method'     => 'post',
          'controller' => 'async',
          'action'     => 'languagesSet')
  );
}

?>
