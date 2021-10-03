<?php

class MediaHelper extends Helper {

  public function __construct() {
    parent::__construct();

    Trans::initTranslations();
  }


  public function getResponseStatus($id) {

    $response = new stdClass();

    $response->status = $id;

    switch ($id) {
      case MediaErrors::OK:
        $response->message = Trans::get('File uploaded successfully');
        break;
      case MediaErrors::MISSING_FILE:
        $response->message = Trans::get('The file was not uploaded');
        break;
      case MediaErrors::BAD_FORMAT:
        $response->message = Trans::get('The file is not in the allowed format');
        break;
      case MediaErrors::EXCEEDED_SIZE:
        $response->message = Trans::get('The image must not be larger than') . ' ' . Conf::get('avatar_file_size') . 'mb';
        break;
      default:
        $response->message = 'Ok';
        break;
    }

    return $response;
  }

  public function getUploadResponseStatus($error) {

    $response = new stdClass();

    $response->status = $error;

    switch ($error) {
      case UPLOAD_ERR_INI_SIZE:
        $response->message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        break;
      case UPLOAD_ERR_FORM_SIZE:
        $response->message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        break;
      case UPLOAD_ERR_PARTIAL:
        $response->message = 'The uploaded file was only partially uploaded';
        break;
      case UPLOAD_ERR_NO_FILE:
        $response->message = 'No file was uploaded';
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        $response->message = 'Missing a temporary folder';
        break;
      case UPLOAD_ERR_CANT_WRITE:
        $response->message = 'Failed to write file to disk';
        break;
      case UPLOAD_ERR_EXTENSION:
        $response->message = 'File upload stopped by extension';
        break;

      default:
        $response->message = 'Unknown upload error';
        break;
    }
    return $response;
  }

}

?>