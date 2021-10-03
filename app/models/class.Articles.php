<?php

class Articles extends Model {

  public $id;
  public $title;
  public $subtitle;
  public $alias;
  public $parentId = 0;
  public $parentName;
  public $parentLangGroupId;
  public $parentLangId;
  public $categoryParentId = 0;
  public $categoryParentName;
  public $categoryParentLangGroupId;
  public $categoryParentLangId;
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
  public $eventDate;
  public $publishDate;
  public $cdate;
  public $udate;
  public $url;
  public $gallery;
  public $galleryJson;
  public $allowComments;
  public $comments;

  private $selectQueryString;
  private $selectQueryCountString;
  private $orderByString;

  public function __construct() {
    parent::__construct();
    $this->setTable('articles');
    $this->setQueryStrings();
  }

  public function map($data) {
    if (is_array($data)) $data = (object)$data;

    if (@exists($data->id)) $this->id = (int)$data->id;
    if (@exists($data->title)) $this->title = (string)$data->title;
    if (@exists($data->subtitle)) $this->subtitle = (string)$data->subtitle;
    if (@exists($data->alias)) $this->alias = (string)$data->alias;
    if (@exists($data->category_id)) $this->parentId = (int)$data->category_id;
    if (@exists($data->parent_name)) $this->parentName = (string)$data->parent_name;
    if (@exists($data->parent_lang_group_id)) $this->parentLangGroupId = (int)$data->parent_lang_group_id;
    if (@exists($data->parent_lang_id)) $this->parentLangId = (int)$data->parent_lang_id;
    if (@exists($data->category_parent_id)) $this->categoryParentId = (int)$data->category_parent_id;
    if (@exists($data->category_parent_name)) $this->categoryParentName = (string)$data->category_parent_name;
    if (@exists($data->category_parent_lang_group_id)) $this->categoryParentLangGroupId = (int)$data->category_parent_lang_group_id;
    if (@exists($data->category_parent_lang_id)) $this->categoryParentLangId = (int)$data->category_parent_lang_id;
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
    if (@exists($data->event_date)) $this->eventDate = $data->event_date;
    if (@exists($data->publish_date)) $this->publishDate = $data->publish_date;
    if (@exists($data->cdate)) $this->cdate = $data->cdate;
    if (@exists($data->udate)) $this->udate = $data->udate;
    if (@exists($data->url)) $this->url = (string)$data->url;
    if (@exists($data->gallery_json)) $this->galleryJson = $data->gallery_json;
    if (@exists($data->gallery)) $this->gallery = $data->gallery;
    if (@exists($data->allow_comments)) $this->allowComments = (int)$data->allow_comments;
    if (@exists($data->comments)) $this->comments = $data->comments;
  }

  public function getTotal($data, $whereColumns = null) {

    $columns = array(
      array('columnAlias' => 'a', 'columnName' => 'title'),
      array('columnAlias' => 'a', 'columnName' => 'subtitle'),
      array('columnAlias' => 'a', 'columnName' => 'intro_text')
    );

    return $this->getTotalItems($data, $columns, $this->selectQueryCountString, $whereColumns);
  }

  public function getWithFilters($data, $whereColumns = null) {

    $columns = array(
      array('columnAlias' => 'a', 'columnName' => 'title'),
      array('columnAlias' => 'a', 'columnName' => 'subtitle'),
      array('columnAlias' => 'a', 'columnName' => 'intro_text')
    );

    return $this->getItemsWithFilters($data, $columns, $this->selectQueryString, $whereColumns);
  }

  public function getOne($id, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `a`.`id` = :id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `a`.`published` = 1';
    }

    return $this->exafe($sql, array('id' => $id));
  }

  public function getAll($fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' WHERE `a`.`published` = 1';
    }

    return $this->exafeAll($sql);
  }

  public function getByParentId($parentId, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `a`.`category_id` = :category_id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `a`.`published` = 1';
    }

    $sql .= $this->orderByString;

    return $this->exafeAll($sql, array('category_id' => $parentId));
  }

  public function getByAlias($alias, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `a`.`alias` = :alias';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `a`.`published` = 1';
    }

    return $this->exafe($sql, array('alias' => $alias));
  }

  public function getByLangGroupId($id, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `a`.`id` = :id || `a`.`lang_group_id` = :id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `a`.`published` = 1';
    }

    return $this->exafeAll($sql, array('id' => $id));
  }

  public function getOneByLangId($id, $langId, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE (`a`.`id` = :id || `a`.`lang_group_id` = :id) AND `a`.`lang_id` = :lang_id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `a`.`published` = 1';
    }

    return $this->exafe($sql, array('id' => $id, 'lang_id' => $langId));
  }

  public function getAllByLangId($langId, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `a`.`lang_id` = :lang_id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `a`.`published` = 1';
    }

    return $this->exafeAll($sql, array('lang_id' => $langId));
  }

  public function getAliasesByParentId($parentId) {

    $sql = 'SELECT `id`, `alias` FROM `articles` WHERE `category_id` = :category_id;';
    return $this->exafeAll($sql, array('category_id' => $parentId));
  }

  public function getByLanguageGroupIdAndLanguageId($langGroupId, $langId, $fetchWithUnpublished = null) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE (`a`.`id` = :lang_group_id || `a`.`lang_group_id` = :lang_group_id) AND `a`.`lang_id` = :lang_id';

    if(!@exists($fetchWithUnpublished)) {
      $sql .= ' AND `a`.`published` = 1';
    }

    return $this->exafe($sql, array('lang_group_id' => $langGroupId, 'lang_id' => $langId));
  }

  public function updateLangGroupId($id) {
    $sql = 'UPDATE `articles` SET `lang_group_id` = :id WHERE `id` = :id;';
    $this->execute($sql, array('id' => $id));
  }

  /************************************ OTHER ************************************/

  private function setQueryStrings() {

    $this->selectQueryString = 'SELECT `a`.*, 
                                `c1`.`id` AS `parent_id`, `c1`.`name` AS `parent_name`, `c1`.`lang_group_id` AS `parent_lang_group_id`, `c1`.`lang_id` AS `parent_lang_id`,
                                `c2`.`id` AS `category_parent_id`, `c2`.`name` AS `category_parent_name`, `c2`.`parent_id` AS `category_parent_parent_id`, `c2`.`lang_group_id` AS `category_parent_lang_group_id`, `c2`.`lang_id` AS `category_parent_lang_id`,
                                `l`.`name` AS `language_name`, `l`.`alias` AS `language_alias`
                                FROM `articles` AS `a` 
                                LEFT JOIN `categories` AS `c1` ON `a`.`category_id` = `c1`.`id` 
                                LEFT JOIN `categories` AS `c2` ON `c1`.`parent_id` = `c2`.`id`
                                LEFT JOIN `languages` AS `l` ON `l`.`id` = `a`.`lang_id`';

    $this->selectQueryCountString = 'SELECT COUNT(`a`.`id`) AS `total` FROM `articles` AS `a` LEFT JOIN `categories` AS `c` ON `a`.`category_id` = `c`.`id`';

    $this->orderByString = ' ORDER BY `a`.`rang`, `a`.`id` DESC';
  }
}

?>