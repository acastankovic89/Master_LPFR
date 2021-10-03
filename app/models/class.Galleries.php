<?php

class Galleries extends Model {

  public $id;
  public $name;

  public function __construct() {
    parent::__construct();
    $this->setTable('galleries');
  }

  public function map($data) {
    if (is_array($data)) $data = (object)$data;

    if (@exists($data->id)) $this->id = (int)$data->id;
    if (@exists($data->name)) $this->name = (string)$data->name;
  }

  public function getOne($id) {

    $sql = 'SELECT * FROM `galleries` WHERE `id` = :id';
    return $this->exafe($sql, array('id' => $id));
  }


  public function getAll() {
    $sql = 'SELECT * FROM `galleries`';
    return $this->exafeAll($sql, null);
  }

}
?>