<?php


class GalleriesService extends Service {

  private $model;
  private $service;

  public function __construct() {

    $model = Galleries::Instance();
    if ($model instanceof Galleries) {
      $this->model = $model;
    }

    $service = Service::Instance();
    if ($service instanceof Service) {
      $this->service = $service;
    }
  }

  public function parseGalleryShortCodes($content) {

    $mv = MainView::Instance();
    if ($mv instanceof MainView) $mainView = $mv;

    //parse for gallery short code
    $subject = $content;

    $pattern = '/{' . Conf::get('nc_gallery_label') . '=(.*?)}/';

    preg_match_all($pattern, $subject, $matches, PREG_PATTERN_ORDER);

    foreach ($matches[1] as $match) {
      $item = $this->model->getOne($match);
      $gal = $this->service->setGallery($item);
      $gallery = $mainView->renderGallery($gal);

      $content = str_replace('{' . Conf::get('nc_gallery_label') . '=' . $match . '}', $gallery, $content);
    }

    return $content;
  }
}

?>