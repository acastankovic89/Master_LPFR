<?php


class Trans {

  static public $translations = array();
  static private $language;
  static private $langId;
  static private $langName;
  static private $langAlias;

  public static function get($key, $group = null) {

    if (!@exists($group)) $group = 'default';
    $result = @exists(self::$translations[$group][$key]) ? self::$translations[$group][$key] : $key;
    return $result;
  }

  public static function set($value, $key, $group = null) {

    if (!@exists($group)) $group = 'default';
    if (!@exists(self::$translations[$group])) self::$translations[$group] = array();
    self::$translations[$group][$key] = $value;
  }

  public static function setLanguage($language) {

    if (!@exists($language)) return;

    if (!is_array($language)) $language = (array)$language;

    $_SESSION[Conf::get('session_prefix') . 'language'] = $language;
    self::$language = $language;

    self::$langId = self::$language['id'];
    self::$langName = self::$language['name'];
    self::$langAlias = self::$language['alias'];
  }

  public static function getLanguage() {
    return self::$language;
  }

  public static function getLanguageId() {
    return self::$langId;
  }

  public static function getLanguageName() {
    return self::$langName;
  }

  public static function getLanguageAlias() {
    return self::$langAlias;
  }

  public static function initFromFile() {

    $langFolder = Conf::get('root') . '/languages/' . self::$langAlias;

    if ($handle = opendir($langFolder)) {

      while (false !== ($entry = readdir($handle))) {
        if ($entry != '.' && $entry != '..') {

          $fHandle = fopen($langFolder . '/' . $entry, 'r');
          if ($fHandle) {

            while (($line = fgets($fHandle)) !== false) {

              $position = strpos($line, '=');
              $key = trim(substr($line, 0, $position));
              $value = substr($line, $position + 1);
              $value = trim($value);

              self::set($value, $key, str_replace('.txt', '', $entry));
            }
            fclose($fHandle);
          }
          else {
            Logger::put('Error opening file: ' . $langFolder . '/' . $entry);
          }

        }
      }
      closedir($handle);
    }
    else {
      Logger::put('No language data for: ' . self::$langAlias);
    }
  }

  public static function initLanguage() {

    if (@exists($_SESSION[Conf::get('session_prefix') . 'language'])) $lang = $_SESSION[Conf::get('session_prefix') . 'language'];
    else $lang = Conf::get('language_default');
    self::setLanguage($lang);
  }

  public static function initTranslations() {
    Util::sessionStart();
    self::initLanguage();
    self::initFromFile();
  }

  public static function setLanguageById($pageLangId = null) {

    if (!@exists($pageLangId)) return;

    if ((int)$pageLangId === (int)self::$langId) return;

    $language = Dispatcher::instance()->dispatch('languages', 'fetchOne', array('id' => $pageLangId));

    unset($language->active);
    unset($language->translations);

    self::setLanguage($language);
    self::initFromFile();
  }

  public static function setLanguageByAlias($pageLangAlias = null) {

    if (!@exists($pageLangAlias)) return;

    if (strtolower($pageLangAlias) === strtolower(self::$langAlias)) return;

    $language = Dispatcher::instance()->dispatch('languages', 'fetchByAlias', array('alias' => $pageLangAlias));

    unset($language->active);
    unset($language->translations);

    self::setLanguage($language);
    self::initFromFile();
  }

}
?>