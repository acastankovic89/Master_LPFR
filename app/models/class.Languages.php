<?php


class Languages extends Model {

  static public $activeLanguages;
  static public $db;

  public function __construct() {
    parent::__construct();
    $this->setTable('languages');
  }


  /************************************ FETCH ************************************/


  private static function setDBConn() {

    if (!isset(self::$db)) self::$db = DB::Connect();

    return self::$db;
  }


  public function getOne($id) {

    $sql = 'SELECT * FROM `languages` WHERE `id` = :id';

    return $this->exafe($sql, array('id' => $id));
  }


  public function getAll() {

    $sql = 'SELECT * FROM `languages`';

    return $this->exafeAll($sql);
  }


  public function getByAlias($alias) {

    $sql = 'SELECT * FROM `languages` WHERE `alias` = :alias';

    return $this->exafe($sql, array('alias' => $alias));
  }


  public static function getActive() {

    if (!isset(self::$activeLanguages)) {

      self::setDBConn();

      try {

        $sql = 'SELECT * FROM `languages` WHERE `active` = :active';

        $stm = self::$db->prepare($sql);
        $stm->execute(array('active' => 1));

        $results = self::fetchAll($stm);

        $languages = self::sortActiveLanguagesByCurrent($results);

        if (@exists($languages)) {

          $currentAlias = Trans::getLanguageAlias();

          foreach ($languages as $lang) {

            $lang->nameTranslated = $lang->name;
            $lang->aliasNameTranslated = $lang->alias;

            if (isset($lang->translations)) {

              $translations = json_decode($lang->translations);

              $name = !is_array($translations->name) ? (array)$translations->name : $translations->name;
              $aliasName = !is_array($translations->alias_name) ? (array)$translations->alias_name : $translations->alias_name;

              $lang->nameTranslated = $name[$currentAlias];
              $lang->aliasNameTranslated = $aliasName[$currentAlias];
            }
          }
        }

        self::$activeLanguages = $languages;

        return self::$activeLanguages;
      }
      catch (PDOException $e) {
        self::HandleDBError($e);
        return false;
      }
    }
    else return self::$activeLanguages;
  }


  private static function sortActiveLanguagesByCurrent($languages) {

    if (!isset($languages) || empty($languages)) return null;

    $langId = Trans::getLanguageId();

    if ((int)$languages[0]->id === (int)$langId) return $languages;

    $sortedLanguages = array();
    foreach ($languages as $lang) {
      if ((int)$lang->id === (int)$langId) {
        array_push($sortedLanguages, $lang);
      }
    }

    foreach ($languages as $lang) {
      if ((int)$lang->id !== (int)$langId) {
        array_push($sortedLanguages, $lang);
      }
    }

    return $sortedLanguages;
  }


  public static function enabled() {

    $multilingualEnabled = Conf::get('multilingual_enabled');

    if (!@exists($multilingualEnabled) || !$multilingualEnabled) return false;

    $languages = self::getActive();

    if (!@exists($languages)) return false;
    if (count($languages) === 1) return false;

    return true;
  }
}

?>