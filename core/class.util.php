<?php


class Util {


  /***************************** MEDIA *****************************/

  public static function mediaImageExists($image, $thumb = null) {

    if(!@exists($image)) return false;

    $root = Conf::get('media_root') . '/';
    if (@exists($thumb) && $thumb) {
      $root = Conf::get('media_thumbs_root') . '/';
    }

    if(!file_exists($root . '/' . $image)) return false;
    return true;
  }


  public static function setMediaImageUrl($image, $thumb = null) {

    if(!self::mediaImageExists($image, $thumb)) {
      return Conf::get('css_img_url') . '/no-image.png';
    }

    $url = Conf::get('media_url');
    if (@exists($thumb) && $thumb) {
      $url = Conf::get('media_thumbs_url');
    }

    return $url . '/' . $image;
  }

  /************************** /END OF MEDIA **************************/


  /***************************** FORMAT *****************************/

  public static function formatDate($date, $format = null) {
    if (!isset($format)) $format = 'd.m.Y.';
    return date_format(date_create($date), $format);
  }


  public static function formatPrice($price) {

    $alias = (string)Trans::getLanguageAlias();

    if ($alias === 'sr' || $alias === 'ср') {
      return number_format($price, 2, ',', '.');
    }
    else {
      return number_format($price, 2, '.', ',');
    }
  }


  public static function formatCleanUrl($title) {

    $title = trim($title);
    $filter1 = '/[^\-\s\pN\pL]+/u'; // only letters, numbers, spaces, hyphens
    $filter2 = '/[\-\s]+/';         // remove spaces and duplicate hyphens
    $alias = preg_replace($filter1, '', mb_strtolower($title, 'UTF-8'));
    $alias = preg_replace($filter2, '-', $alias);
    $alias = trim($alias, '-');
    $find = array('ž', 'ć', 'č', 'š', 'đ', 'ä', 'ü');
    $replace = array('z', 'c', 'c', 's', 'd', 'a', 'u');
    $alias = str_replace($find, $replace, $alias);

    return $alias;
  }

  /************************* /END OF FORMAT *************************/


  /****************************** DATE ******************************/


  public static function setDate($date) {

    if (@exists($date)) {
      return self::formatDate($date, 'Y-m-d');
    }
    return date('Y-m-d H:i:s');
  }


  public static function getDayFromDatetime($dateTime) {

    if ($dateTime == '0000-00-00 00:00:00') return null;
    $date = self::formatDate($dateTime);
    $dateArray = explode('.', $date);
    return $dateArray['0'];
  }


  public static function getMonthFromDatetime($dateTime) {

    if ($dateTime == '0000-00-00 00:00:00') return null;
    $date = self::formatDate($dateTime);
    $dateArray = explode('.', $date);
    return $dateArray['1'];
  }


  public static function getYearFromDatetime($dateTime) {

    if ($dateTime == '0000-00-00 00:00:00') return null;
    $date = self::formatDate($dateTime);
    $dateArray = explode('.', $date);
    return $dateArray['2'];
  }

  /*************************** /END OF DATE ***************************/


  /************************* CATEGORIES TREE *************************/

  public static function formTree($items, $rootId = 0) {

    if (!isset($rootId)) $rootId = 0;

    $hash = array();
    foreach ($items as $object) {

      if (is_object($object)) $object = (array)$object;

      if (isset($object['title'])) $object['name'] = $object['title'];

      $object['text'] = $object['name'];
      $object['icon'] = 'fa fa-folder c-primary';
      $object['state'] = false;

      $hash[$object['id']] = $object;
    }

    $tree = array();

    // build tree from hash
    foreach ($hash as $id => &$node) {

      if (isset($node['parent_id']) && $parent = $node['parent_id']) $hash[$parent]['children'][] =& $node;
      else if (isset($node['parentId']) && $parent = $node['parentId']) $hash[$parent]['children'][] =& $node;
      else $tree[] =& $node;
    }
    unset($node, $hash);

    $result = null;
    $rootItem['id'] = 0;
    $rootItem['children'] = $tree;

    if ($rootId == 0) $result = $tree;
    else {
      $result = self::findInTree($rootId, $rootItem);
      $result = array($result);
    }

    return $result;
  }


  public static function findInTree($id, $item) {

    if ((isset($item['id']) && (int)$item['id'] === (int)$id) || (isset($item['lang_group_id']) && (int)$item['lang_group_id'] === (int)$id)) return $item;
    else {
      if (isset($item['children'])) {
        foreach ($item['children'] as $child) {
          $res = self::findInTree($id, $child);
          if ($res) return $res;
        }
      }
    }
    return null;
  }

  /********************* /END OF CATEGORIES TREE *********************/


  /***************************** BASE 64 *****************************/

  public static function uploadBase64File($string, $dir, $fileName) {

    $data = explode(';base64,', $string);

    $imageBase64 = base64_decode($data[1]);

    $file = $dir . DIRECTORY_SEPARATOR . $fileName;

    file_put_contents($file, $imageBase64);

    return $file;
  }


  public static function getBase64FileExtension($string) {

    $arr = explode(';base64', $string);
    $file = $arr[0];
    $ext = explode('/', $file)[1];

    return $ext;
  }

  /************************* /END OF BASE 64 *************************/


  public static function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }


  public static function truncateString($string, $max = 255) {
    if (mb_strlen($string, 'utf-8') >= $max) {
      $string = mb_substr(strip_tags($string), 0, $max - 5, 'utf-8') . '...';
    }
    return $string;
  }


  public static function getCurrentUrl() {

    $uri = ltrim(urldecode($_SERVER['REQUEST_URI']), '/');

    $base = Conf::get('base');

    if (isset($base) && (string)$base !== '') {
      if (strpos($uri, Conf::get('base')) !== false) {
        $uri = str_replace(Conf::get('base'), '', $uri);
      }
    }

    $url = Conf::get('url') . $uri;

    return $url;
  }

  public static function sessionStart() {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }


  public static function parseLink($link) {

    return (substr($link, 0, 7) === 'http://') || (substr($link, 0, 8) === 'https://') ? $link : Conf::get('url') . '/' . $link;
  }


  public static function trimFields($data) {

    $keys = array_keys($data);

    for ($i = 0; $i < count($keys); $i++) {
      $data[$keys[$i]] = trim($data[$keys[$i]]);
    }

    return $data;
  }


  public static function unsetEmptyFields($data) {

    $keys = array_keys($data);

    for ($i = 0; $i < count($keys); $i++) {

      if ($data[$keys[$i]] == '') {
        unset($data[$keys[$i]]);
      }
    }

    return $data;
  }


  public static function getClientIP() {

    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
  }


  public static function setHtmlTagLangAttr() {

    $lang = Trans::getLanguageAlias();

    if ((string)$lang === 'ср') {
      $lang = 'sr';
    }

    echo $lang;
  }


  public static function langSuffix($value = null) {

    $langId = @exists($value) ? $value : Trans::getLanguageId();

    return '-langId-' . $langId;
  }


  public static function exportFile($data, $heading = null, $preHeading = null) {

    if (@exists($preHeading)) {
      echo $preHeading . "\n";
    }

    if (@exists($heading)) {
      echo implode("\t", $heading) . "\n";
    }

    if (@exists($data)) {
      foreach ($data as $row) {
        echo implode("\t", array_values($row)) . "\n";
      }
    }
    else {
      echo 'No data available in table';
    }
    exit;
  }


  public static function IEBrowser() {
    return preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false);
  }


}
?>