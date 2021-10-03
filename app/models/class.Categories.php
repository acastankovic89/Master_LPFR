<?php

class Categories extends Model {

  public $id;
  public $name;
  public $subtitle;
  public $alias;
  public $parentId = 0;
  public $parentName;
  public $parentLangGroupId;
  public $parentLangId;
  public $langGroupId;
  public $langId;
  public $introText;
  public $content;
  public $introImage;
  public $mainImage;
  public $metaTitle;
  public $metaKeywords;
  public $metaDescription;
  public $published;
  public $cdate;
  public $udate;
  public $url;
  public $gallery;
  public $galleryJson;

  private $selectQueryString;
  private $selectQueryCountString;
  private $orderByString;

  static public $categories;
  static public $db;

  public function __construct() {
    parent::__construct();
    $this->setTable('categories');
    $this->setQueryStrings();
  }

  public function map($data) {
    if (is_array($data)) $data = (object)$data;

    if (@exists($data->id)) $this->id = (int)$data->id;
    if (@exists($data->subtitle)) $this->subtitle = (string)$data->subtitle;
    if (@exists($data->name)) $this->name = (string)$data->name;
    if (@exists($data->alias)) $this->alias = (string)$data->alias;
    if (@exists($data->parent_id)) $this->parentId = (int)$data->parent_id;
    if (@exists($data->parent_name)) $this->parentName = (string)$data->parent_name;
    if (@exists($data->parent_lang_group_id)) $this->parentLangGroupId = (int)$data->parent_lang_group_id;
    if (@exists($data->parent_lang_id)) $this->parentLangId = (int)$data->parent_lang_id;
    if (@exists($data->lang_group_id)) $this->langGroupId = (int)$data->lang_group_id;
    if (@exists($data->lang_id)) $this->langId = (int)$data->lang_id;
    if (@exists($data->intro_text)) $this->introText = (string)$data->intro_text;
    if (@exists($data->content)) $this->content = (string)$data->content;
    if (@exists($data->intro_image)) $this->introImage = (string)$data->intro_image;
    if (@exists($data->main_image)) $this->mainImage = (string)$data->main_image;
    if (@exists($data->meta_title)) $this->metaTitle = (string)$data->meta_title;
    if (@exists($data->meta_keywords)) $this->metaKeywords = (string)$data->meta_keywords;
    if (@exists($data->meta_description)) $this->metaDescription = (string)$data->meta_description;
    if (@exists($data->published)) $this->published = (int)$data->published;
    if (@exists($data->cdate)) $this->cdate = $data->cdate;
    if (@exists($data->udate)) $this->udate = $data->udate;
    if (@exists($data->url)) $this->url = (string)$data->url;
    if (@exists($data->gallery_json)) $this->galleryJson = $data->gallery_json;
    if (@exists($data->gallery)) $this->gallery = $data->gallery;
  }

  private static function setDBConn() {

    if (!@exists(self::$db)) {
      self::$db = DB::Connect();
    }
    return self::$db;
  }

  public static function getAllCategories($langId = null) {

//    if (@exists(self::$categories)) {
//      return self::$categories;
//    }

    $getCategories = false;

    if (@exists($langId)) {
      $getCategories = true;
    }

    if (!@exists(self::$categories)) {
      $getCategories = true;
    }

    if (!$getCategories) {
      return self::$categories;
    }

    self::setDBConn();

    try {
      $sql = 'SELECT `c1`.*, 
              `c2`.`name` AS `parent_name`, `c2`.`alias` AS `parent_alias`, `c2`.`lang_group_id` AS `parent_lang_group_id`
              FROM `categories` `c1` 
              LEFT JOIN `categories` `c2` ON `c1`.`parent_id` = `c2`.`id`';


      $stm = self::$db->prepare($sql);

      if (isset($langId)) $stm->bindValue(':lang_id', (int)$langId, PDO::PARAM_INT);

      $stm->execute();

      self::$categories = self::fetchAll($stm);

      return self::$categories;
    }
    catch (PDOException $e) {
      self::HandleDBError($e);
      return false;
    }
  }

  public function getTotal($data) {

    $columns = array(array('columnAlias' => 'c1', 'columnName' => 'name'));
    return $this->getTotalItems($data, $columns, $this->selectQueryCountString);
  }

  public function getWithFilters($data) {

    $columns = array(array('columnAlias' => 'c1', 'columnName' => 'name'));
    return $this->getItemsWithFilters($data, $columns, $this->selectQueryString);
  }

  public function getOne($id, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `c1`.`id` = :id';

    if (!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `c1`.`published` = 1';
    }

    return $this->exafe($sql, array('id' => $id));
  }

  public function getAll($fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;

    if (!@exists($fetchWithUnpublished)) {
      $sql .= ' WHERE `c1`.`published` = 1';
    }

    $sql .= $this->orderByString;

    return $this->exafeAll($sql);
  }

  public function getByParentId($parentId, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `c1`.`parent_id` = :parent_id';

    if (!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `c1`.`published` = 1';
    }

    $sql .= $this->orderByString;

    return $this->exafeAll($sql, array('parent_id' => $parentId));
  }

  public function getByAlias($alias, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `c1`.`alias` = :alias';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `c1`.`published` = 1';
    }

    return $this->exafe($sql, array('alias' => $alias));
  }

  public function getByLangGroupId($id, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `c1`.`id` = :id || `c1`.`lang_group_id` = :id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `c1`.`published` = 1';
    }

    return $this->exafeAll($sql, array('id' => $id));
  }

  public function getOneByLangId($id, $langId, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE (`c1`.`id` = :id || `c1`.`lang_group_id` = :id) AND `c1`.`lang_id` = :lang_id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `c1`.`published` = 1';
    }

    return $this->exafe($sql, array('id' => $id, 'lang_id' => $langId));
  }

  public function getAllByLangId($langId, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `c1`.`lang_id` = :lang_id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `c1`.`published` = 1';
    }

    return $this->exafeAll($sql, array('lang_id' => $langId));
  }

  public function getByLanguageGroupIdAndLanguageId($langGroupId, $langId, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE (`c1`.`id` = :lang_group_id || `c1`.`lang_group_id` = :lang_group_id) AND `c1`.`lang_id` = :lang_id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `c1`.`published` = 1';
    }

    return $this->exafe($sql, array('lang_group_id' => $langGroupId, 'lang_id' => $langId));
  }

  public function updateLangGroupId($id) {

    $sql = 'UPDATE `categories` SET `lang_group_id` = :id WHERE `id` = :id;';
    $this->execute($sql, array('id' => $id));
  }

  /************************************ OTHER ************************************/


  private function setQueryStrings() {

    $this->selectQueryString = 'SELECT `c1`.*, 
                                `c2`.`name` AS `parent_name`, `c2`.`lang_group_id` AS `parent_lang_group_id`, `c2`.`lang_id` AS `parent_lang_id`,
                                `l`.`name` AS `language_name`, `l`.`alias` AS `language_alias`
                                FROM `categories` `c1` 
                                LEFT JOIN `categories` `c2` ON `c1`.`parent_id` = `c2`.`id`
                                LEFT JOIN `languages` AS `l` ON `l`.`id` = `c1`.`lang_id`';

    $this->selectQueryCountString = 'SELECT COUNT(`c1`.`id`) AS `total` FROM `categories` `c1` LEFT JOIN `categories` `c2` ON `c1`.`parent_id` = `c2`.`id`';

    $this->orderByString = ' ORDER BY `c1`.`rang`, `c1`.`name`';
  }

}

?>