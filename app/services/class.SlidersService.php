<?php

class SlidersService extends Service {

  private $slidersModel;
  private $sliderItemsModel;

  public function __construct() {

    $slidersModel = Sliders::Instance();
    if ($slidersModel instanceof Sliders) {
      $this->slidersModel = $slidersModel;
    }

    $sliderItemsModel = SliderItems::Instance();
    if ($sliderItemsModel instanceof SliderItems) {
      $this->sliderItemsModel = $sliderItemsModel;
    }
  }

  public function getGroup($id) {

    $langGroup = null;

    $item = $this->slidersModel->getOne($id);

    if(@exists($item) && (bool)$item !== false) {

      $langGroupId = $this->setLanguageGroupId($item);
      $langGroup = $this->slidersModel->getByLangGroupId($langGroupId);
    }

    return $this->setGroupItemsByLang($langGroup);
  }

  public function insert($data) {

    $this->slidersModel->insert($data);

    $id = $this->slidersModel->lastInsertId();

    if(!@exists($data['lang_group_id']) || (int)$data['lang_group_id'] === 0) {

      $this->slidersModel->updateLangGroupId($id);
    }

    return $id;
  }

  public function setItem($item) {

    if(@exists($item) && $item) {
      $sliderItem = new SliderItems();
      $sliderItem->map($item);
      return $sliderItem;
    }

    return null;
  }

  public function setItems($items) {

    if(@exists($items) && $items) {

      $data = array();
      foreach ($items as $item) {
        $sliderItem = new SliderItems();
        $sliderItem->map($item);
        array_push($data, $sliderItem);
      }

      return $data;
    }

    return null;
  }
}

?>