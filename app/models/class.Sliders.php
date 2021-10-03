<?php

class Sliders extends Model {

  private $selectQueryString;
  private $selectQueryCountString;
  private $orderByString;

  public function __construct() {
    parent::__construct();
    $this->setTable('sliders');
    $this->setQueryStrings();
  }

  public function getTotal($data, $whereColumns = null) {

    $columns = array(
      array('columnName' => 'name')
    );

    return $this->getTotalItems($data, $columns, $this->selectQueryCountString, $whereColumns);
  }

  public function getWithFilters($data, $whereColumns = null) {

    $columns = array(
      array('columnAlias' => 's', 'columnName' => 'name')
    );

    return $this->getItemsWithFilters($data, $columns, $this->selectQueryString, $whereColumns);
  }

  public function getOne($id) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `s`.`id` = :id';

    return $this->exafe($sql, array('id' => $id));
  }

  public function getByLangGroupId($id) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `s`.`id` = :id || `s`.`lang_group_id` = :id';

    return $this->exafeAll($sql, array('id' => $id));
  }

  public function getByLangGroupIdAndLangId($id, $langId) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE (`s`.`id` = :id || `s`.`lang_group_id` = :id) AND `lang_id` = :lang_id';

    return $this->exafe($sql, array('id' => $id, 'lang_id' => $langId));
  }

  public function updateLangGroupId($id) {

    $sql = 'UPDATE `sliders` SET `lang_group_id` = :id WHERE `id` = :id;';
    $this->execute($sql, array('id' => $id));
  }

  /************************************ OTHER ************************************/

  private function setQueryStrings() {

    $this->selectQueryString = 'SELECT `s`.*, 
                                `l`.`name` AS `language_name`, `l`.`alias` AS `language_alias` 
                                FROM `sliders` AS `s` 
                                LEFT JOIN `languages` AS `l` ON `s`.`lang_id` = `l`.`id`';

    $this->selectQueryCountString = 'SELECT COUNT(`id`) AS `total` FROM `sliders`';
  }


}

?>