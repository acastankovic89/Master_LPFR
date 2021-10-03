<?php

class Service {

  protected static $instances;

  public static function Instance() {

    $class = get_called_class();

    if (!isset(self::$instances[$class])) {
      self::$instances[$class] = new $class;
    }
    return self::$instances[$class];
  }

  protected function setAlias($alias, $aliases = null, $id = null) {

    // if aliases do not exist in item's parent category - save alias
    if (!@exists($aliases)) {
      return $alias;
    }

    $aliasExists = false;
    foreach ($aliases as $item) {

      if ((string)$alias === (string)$item->alias && (int)$id !== (int)$item->id) {

        $aliasExists = true;
      }
    }

    // if alias do not exist in parent category - save alias
    if (!$aliasExists) {
      return $alias;
    }

    // if alias exist in parent category - create new alias
    $aliasesArray = array();
    foreach ($aliases as $item) {
      array_push($aliasesArray, $item->alias);
    }

    $newAlias = $this->findAvailableAlias($aliasesArray, $alias);
    return $newAlias;
  }

  protected function findAvailableAlias($aliases, $alias) {

    if (in_array($alias, $aliases)) {
      $counter = 1;

      while (in_array(($alias . '-' . ++$counter), $aliases)) ;

      $alias .= '-' . $counter;
    }

    return $alias;
  }

  protected function setGallery($data) {

    $galleryArray = array();

    $galleryJson = null;
    if(@exists($data->gallery_json)) $galleryJson = $data->gallery_json;
    else if (@exists($data->galleryJson)) $galleryJson = $data->galleryJson;

    if (@exists($galleryJson)) {

      $gallery = json_decode($galleryJson);

      if (@exists($gallery)) {

        foreach ($gallery as $g) {

          $item = new stdClass();
          $item->type = $g->type;

          if ((string)$g->type === 'media') {

            if (@exists($g->id)) {
              $item->id = $g->id;
            }

            if (@exists($g->description)) {
              $item->description = $g->description;
            }

            if (@exists($g->value)) {

              $item->value = $g->value;

              $item->url = Util::setMediaImageUrl($g->value);
              $item->thumbUrl = Util::setMediaImageUrl($g->value, true);
            }
          }
          else if ((string)$g->type === 'youtube_video') {

            if (@exists($g->description)) {
              $item->description = $g->description;
            }

            if (@exists($g->value)) {
              $item->value = $g->value;
              $item->embedUrl = 'https://www.youtube.com/embed/' . $g->value;
              $item->watchUrl = 'https://www.youtube.com/watch?v=' . $g->value;
              $item->imageUrl = 'http://img.youtube.com/vi/' . $g->value . '/0.jpg';
            }
          }

          array_push($galleryArray, $item);
        }
      }
    }

    return $galleryArray;
  }


  public function setLangIdParam($params) {
    return @exists($params['lang_id']) ? $params['lang_id'] : Trans::getLanguageId();
  }

  public function setGroupItemsByLang($items = null) {

    $languages = Languages::getActive();

    $newItems = array();

    // create default items array
    foreach ($languages as $lang) {

      $langId = $lang->id;

      $newItems[$langId] = new stdClass();
      $newItems[$langId]->id = 0;
      $newItems[$langId]->lang_id = $lang->id;
    }


    // populate array with existing items from base
    if (@exists($items)) {

      foreach ($items as $item) {

        $langId = $item->lang_id;

        $newItems[$langId] = $item;
      }
    }


    return $newItems;
  }

  public function setLanguageGroupId($item) {
    return @exists($item->lang_group_id) ? $item->lang_group_id : $item->id;
  }

  public function setLangId($data = null) {
    return @exists($data) && @exists($data['lang_id']) ? $data['lang_id'] : Trans::getLanguageId();
  }

//  protected function setLanguageGroupIdParams($langGroupId = null, $params = null) {
//
//    $data = array();
//    $data['lang_group_id'] = $langGroupId;
//    if (isset($params['fetchWithUnpublished'])) $data['fetchWithUnpublished'] = $params['fetchWithUnpublished'];
//
//    return $data;
//  }

//  protected function setItemWithLanguageGroupsResponse($results = null, $langGroupId = null) {
//
//    $response = new stdClass();
//    $response->items = $this->setGroupItemsByLang($results);
//    $response->langGroupId = $langGroupId;
//
//    return $response;
//  }
//
//  protected function setLangGroupIdFromParentId($data) {
//
//    if (isset($data['parent_id'])) {
//      $data['lang_group_id'] = $data['parent_id'];
//      unset($data['parent_id']);
//    }
//
//    return $data;
//  }
//
//  protected function setLangGroupIdFromId($data) {
//
//    if (isset($data['id'])) {
//      $data['lang_group_id'] = $data['id'];
//      unset($data['id']);
//    }
//
//    return $data;
//  }
//
//  protected function adminLoggedIn() {
//    $activeSession = Dispatcher::instance()->dispatch('authentication', 'getActiveSession', null);
//    return isset($activeSession) && (bool)$activeSession !== false;
//  }
//
//  protected function getLoggedInUserId() {
//    $activeSession = Dispatcher::instance()->dispatch('authentication', 'getActiveSession', null);
//    if (isset($activeSession) && (bool)$activeSession !== false) {
//      return $activeSession->user_id;
//    }
//    return null;
//  }

  public function setItemComments($item, $commentTypeId) {

    $item->comments = null;
    if (@exists($item->allow_comments) && (int)$item->allow_comments === 1) {
      $item->comments = Dispatcher::instance()->dispatch('comments', 'fetchByTypeIdAndTargetId', array('type_id' => $commentTypeId, 'target_id' => $item->id, 'fetchWithUnpublished' => true));
    }
  }

}
?>