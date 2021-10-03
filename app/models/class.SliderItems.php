<?php


class SliderItems extends Model {

  public $id;
  public $sliderId = 0;
  public $name;
  public $mainImage;
  public $caption;
  public $url;
  public $published;
  public $rang;
  public $cdate;
  public $udate;

  private $selectQueryString;
  private $selectQueryCountString;
  private $orderByString;
  private $wherePublishedString;
  private $andPublishedString;

  public function __construct() {
    parent::__construct();
    $this->setTable('slider_items');
    $this->setQueryStrings();
  }

  public function map($data) {
    if (is_array($data)) $data = (object)$data;

    if (@exists($data->id)) $this->id = (int)$data->id;
    if (@exists($data->slider_id)) $this->sliderId = (int)$data->slider_id;
    if (@exists($data->name)) $this->name = (string)$data->name;
    if (@exists($data->image)) $this->mainImage = (string)$data->image;
    if (@exists($data->caption)) $this->caption = (string)$data->caption;
    if (@exists($data->url)) $this->url = (string)$data->url;
    if (@exists($data->published)) $this->published = (int)$data->published;
    if (@exists($data->rang)) $this->rang = (int)$data->rang;
    if (@exists($data->cdate)) $this->cdate = $data->cdate;
    if (@exists($data->udate)) $this->udate = $data->udate;
  }


  /************************************ FETCH ************************************/

  public function getTotal($data, $whereColumns = null) {

    $columns = array(
      array('columnAlias' => 'si', 'columnName' => 'name')
    );

    return $this->getTotalItems($data, $columns, $this->selectQueryCountString, $whereColumns);
  }

  public function getWithFilters($data, $whereColumns = null) {

    $columns = array(
      array('columnAlias' => 'si', 'columnName' => 'name')
    );

    return $this->getItemsWithFilters($data, $columns, $this->selectQueryString, $whereColumns);
  }

  public function getOne($id) {

    $sql = $this->selectQueryString;
    $sql .= ' WHERE `si`.`id` = :id';
    return $this->exafe($sql, array('id' => $id));
  }


  public function getAll() {

    $sql = $this->selectQueryString;
    $sql .= $this->orderByString;
    return $this->exafeAll($sql);
  }


  public function getBySliderId($sliderId) {

    $sql  = $this->selectQueryString;
    $sql .= 'WHERE `si`.`slider_id` = :slider_id';
    $sql .= $this->orderByString;

    return $this->exafeAll($sql, array('slider_id' => $sliderId));
  }


  /************************************ ACTIONS ************************************/

  public function resetSliderId($sliderId) {

    $sql = 'UPDATE `slider_items` SET `slider_id` = 0 WHERE `slider_id` = :slider_id;';
    $this->execute($sql, array('slider_id' => $sliderId));
  }


  private function updatePositionBySliderId($id, $position, $sliderId) {

    $sql = 'UPDATE `slider_items` SET `rang` = :rang WHERE `id` = :id AND `slider_id` = :slider_id';
    return $this->execute($sql, array('id' => $id, 'rang' => $position, 'slider_id' => $sliderId));
  }

  private function updatePosition($id, $position) {

    $sql = 'UPDATE `slider_items` SET `rang` = :rang WHERE `id` = :id';
    return $this->execute($sql, array('id' => $id, 'rang' => $position));
  }

  public function updatePositions($id, $position) {

    $sliderItem = $this->getOne($id);

    $sql = 'SELECT * FROM `slider_items` WHERE `slider_id` = :slider_id AND `id` != :id ORDER BY rang';

    $sliderItems = $this->exafeAll($sql, array('id' => $id, 'slider_id' => $sliderItem->slider_id));

    $this->updatePosition($id, $position);

    $counter = 0;
    foreach ($sliderItems as $item) {

      if ((int)$counter === (int)$position) {
        $counter++;
      }

      if ((int)$counter !== (int)$item->rang) {

        $this->updatePosition($item->id, $counter);
      }

      $counter++;
    }
  }

  /************************************ OTHER ************************************/


  private function setQueryStrings() {

    $this->selectQueryString = 'SELECT `si`.*, 
                                `s`.`name` AS `slider_name`, `s`.`lang_id` AS `slider_lang_id`, `s`.`lang_group_id` AS `slider_lang_group_id`
                                FROM `slider_items` `si` 
                                LEFT JOIN `sliders` AS `s` ON `s`.`id` = `si`.`slider_id`';

    $this->selectQueryCountString = 'SELECT COUNT(`si`.`id`) AS `total` FROM `slider_items` AS `si` LEFT JOIN `sliders` AS `s` ON `s`.`id` = `si`.`slider_id`';

    $this->wherePublishedString = ' WHERE `si`.`published` = 1';

    $this->andPublishedString = ' AND `si`.`published` = 1';

    $this->orderByString = ' ORDER BY `si`.`rang`, `si`.`id`';
  }
}

?>