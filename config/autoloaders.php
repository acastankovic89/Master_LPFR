<?php


//auto loading functions
Class AppAutoloaders {

  public static function autoload_controllers($class_name) {

    $file = Conf::get("root") . '/app/controllers/class.' . $class_name . '.php';
    if (file_exists($file)) {
      require_once($file);
    }
  }

  public static function autoload_services($class_name) {

    $file = Conf::get("root") . '/app/services/class.' . $class_name . '.php';
    if (file_exists($file)) {
      require_once($file);
    }
  }

  public static function autoload_models($class_name) {

    $file = Conf::get("root") . '/app/models/class.' . $class_name . '.php';
    if (file_exists($file)) {
      require_once($file);
    }
  }

  public static function autoload_helpers($class_name) {

    $file = Conf::get("root") . '/app/helpers/class.' . $class_name . '.php';
    if (file_exists($file)) {
      require_once($file);
    }
  }

  public static function autoload_views($class_name) {

    $file = Conf::get("root") . '/app/views/class.' . $class_name . '.php';
    if (file_exists($file)) {
      require_once($file);
    }
  }

  public static function autoload_layout_views($class_name) {

    $file = Conf::get("root") . '/app/views/layout/class.' . $class_name . '.php';
    if (file_exists($file)) {
      require_once($file);
    }
  }

  public static function autoload_pages_views($class_name) {

    $file = Conf::get("root") . '/app/views/pages/class.' . $class_name . '.php';
    if (file_exists($file)) {
      require_once($file);
    }
  }

  public static function autoload_core($class_name) {

    $file = Conf::get("root") . '/core/class.' . strtolower($class_name) . '.php';
    if (file_exists($file)) {
      require_once($file);
    }
  }

  public static function autoload_libs($class_name) {

    $file = Conf::get("root") . '/core/libs/class.' . strtolower($class_name) . '.php';
    if (file_exists($file)) {
      require_once($file);
    }
  }

  public static function autoload_admin_views($class_name) {

    $file = Conf::get("root") . '/admin/views/class.' . $class_name . '.php';
    if (file_exists($file)) {
      require_once($file);
    }
  }
}

spl_autoload_register('AppAutoloaders::autoload_controllers');
spl_autoload_register('AppAutoloaders::autoload_services');
spl_autoload_register('AppAutoloaders::autoload_models');
spl_autoload_register('AppAutoloaders::autoload_helpers');
spl_autoload_register('AppAutoloaders::autoload_views');
spl_autoload_register('AppAutoloaders::autoload_layout_views');
spl_autoload_register('AppAutoloaders::autoload_pages_views');
spl_autoload_register('AppAutoloaders::autoload_core');
spl_autoload_register('AppAutoloaders::autoload_libs');
spl_autoload_register('AppAutoloaders::autoload_admin_views');

require_once(Conf::get('root') . '/app/app.php');

//register module
$item = new stdClass();
$item->title = 'Normacore';
$item->alias = 'normacore';
$item->root = true;
$modules = Conf::get('modules');
array_push($modules, $item);
Conf::set('modules', $modules);