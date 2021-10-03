<?php

class MediaService extends Service {

   private $model;

   public function __construct() {
     $model = Media::Instance();
     if ($model instanceof Media) {
       $this->model = $model;
     }
   }

   public function upload() {

      $uploadedInDir = false;
      $insertedInDb = false;
      $messages = array();
      $file = null;

      if(!empty($_FILES)) {

         $targetPath = Conf::get('media_root') . '/';

         $size = $_FILES['file']['size'];
         $name = $_FILES['file']['name'];
         $type = $_FILES['file']['type'];

         $mime        = explode('/', $type);
         $mimeType    = $mime[0];
         $mimeSubtype = $mime[1];
         $parsedName  = $this->parseFileName($name);

         $file = $parsedName;

         $targetFile = $targetPath . $parsedName;

         if(!file_exists($targetFile)) {

            $tempFile = $_FILES['file']['tmp_name'];

            $upl = move_uploaded_file($tempFile, $targetFile);

            if($upl && strtolower($mimeType) === 'image') {

               try {

                  $image = new \claviska\SimpleImage();

                  $image
                     ->fromFile($targetFile)
                     ->autoOrient()
                     ->resize(100)
                     ->toFile($targetPath . 'thumbs/' . $parsedName);

                  $uploadedInDir = true;
                  array_push($messages, 'file uploaded in media dir');
               }
               catch(Exception $e) {
                  Logger::put('Error image: ' . $e->getMessage());
                  array_push($messages, 'upload failed');
               }
            }
         }
         else{
            // file exists in media dir
            array_push($messages, 'file exists in media dir');
         }

         if(!$this->mediaExists($parsedName)) {

            $data = array(
               'title'        => $parsedName,
               'file_name'    => $parsedName,
               'mime'         => $type,
               'mime_type'    => $mimeType,
               'mime_subtype' => $mimeSubtype,
               'size'         => $size,
               'rang'         => 1,
               //'created_by'   => $this->getLoggedInUserId()
            );

            $this->model->insert($data);

            $insertedInDb = true;
            array_push($messages, 'media inserted');
         }
         else{
            // media exists in db
            array_push($messages, 'media exists in db');
         }
      }
      else{
         // files not sent
         array_push($messages, 'files not sent');
      }

      $response = new stdClass();
      $response->uploadedInDir = $uploadedInDir;
      $response->insertedInDb = $insertedInDb;
      $response->messages = $messages;
      $response->file = $file;

      return $response;
   }


   public function delete($id) {

      $data = array('id' => $id);

      $media = $this->model->getOne($data);

      $targetFile      = Conf::get('media_root') . '/' . $media->file_name;
      $targetThumbFile = Conf::get('media_thumbs_root') . '/' . $media->file_name;

      if(file_exists($targetFile))      unlink($targetFile);
      if(file_exists($targetThumbFile)) unlink($targetThumbFile);

      $this->model->delete($id);
      return true;
   }


   private function parseFileName($uploadedFile) {

      $fileExtension  = pathinfo($uploadedFile, PATHINFO_EXTENSION);
      $fileParts      = explode('.', $uploadedFile);

      if(count($fileParts) == 2) {

         $fileName = $fileParts[0];
      }
      else{

         $newFileParts = array();
         foreach($fileParts as $part) {

            if($part != $fileExtension) {

               array_push($newFileParts, $part);
            }
         }

         $fileName = implode('', $newFileParts);
      }

      $file = Util::formatCleanUrl($fileName) . '.' . $fileExtension;

      return $file;
   }


   private function mediaExists($fileName) {

      $data = array('file_name' => $fileName);

      $media = $this->model->getByFileName($data);
      return isset($media) &&(bool)$media !== false;
   }


   public function uploadAllowed() {

      if(!@exists($_FILES) || !@exists($_FILES['file'])) {
         return MediaErrors::MISSING_FILE;
      }

      $type = explode('/', $_FILES['file']['type']);
      $t = strtolower($type[1]);
      $size = $_FILES['file']['size'];

      if($t !== 'jpg' && $t !== 'jpeg' && $t !== 'png' && $t !== 'gif') {
         return MediaErrors::BAD_FORMAT;
      }

      if(($size / 1000000) > (int) Conf::get('avatar_file_size')) {
         return MediaErrors::EXCEEDED_SIZE;
      }

      return MediaErrors::OK;
   }


   public function uploadCustom($fileName, $mime, $ext, $base64String = null) {

      $dir = Conf::get('media_root');

      if(@exists($base64String)) {

         // upload in media directory
         Util::uploadBase64File($base64String, $dir, $fileName);
      }

      $file = $dir . DIRECTORY_SEPARATOR . $fileName;

      // upload in media/thumbs directory
      $image = new \claviska\SimpleImage();

      $image
         ->fromFile($file)
         ->autoOrient()
         ->resize(100)
         ->toFile($dir . '/thumbs/' . $fileName);


      // insert in `media` table
      if(!$this->mediaExists($fileName)) {

         $mimeType = $mime . '/' . $ext;

         $data = array(
            'title'        => $fileName,
            'file_name'    => $fileName,
            'mime'         => $mime,
            'mime_type'    => $mimeType,
            'mime_subtype' => $ext,
            'size'         => null,
            'rang'         => 1
         );

         $this->model->insert($data);
      }
   }
}
?>