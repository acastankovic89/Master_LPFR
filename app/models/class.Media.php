<?php

final class MediaErrors {
   const OK = 0;
   const MISSING_FILE = 1;
   const BAD_FORMAT = 2;
   const EXCEEDED_SIZE = 3;
}

class Media extends Model {

   public $id;
   public $title;
   public $size;

   public function __construct() {
      parent::__construct();
      $this->setTable('media');
   }

   public function map($data) {
      if (is_array($data)) $data = (object)$data;

      if (@exists($data->id)) $this->id = (int)$data->id;
      if (@exists($data->title)) $this->title = (string)$data->title;
   }

   public function getOne($data) {

      $sql = 'SELECT * FROM `media` WHERE `id` = :id';
      return $this->exafe($sql, array('id' => $data['id']));
   }

   public function getByFileName($data) {

      $sql = 'SELECT * FROM `media` WHERE `file_name` = :file_name';
      return $this->exafe($sql, array('file_name' => $data['file_name']));
   }

}
?>