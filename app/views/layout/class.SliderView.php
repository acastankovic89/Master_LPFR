<?php

class SliderView extends MainView {

  private $data;

  public function __construct($data) {
    parent::__construct();

    if (@exists($data)) {
      $this->data = $data;
    }
  }

  public function displaySlider() {

    if (@exists($this->data) && @exists($this->data->allItems)) {

      $items = $this->data->allItems;

      echo '<div class="nc-slider-wrapper">';

        echo '<div id="slider">';

        $this->renderSlides($items);

        if (@exists($this->data->show_bullets) && $this->data->show_bullets) {
          $this->renderBulletsNavigation($items);
        }

        if (@exists($this->data->show_arrows) && $this->data->show_arrows) {
          $this->renderArrowsNavigation();
        }
        echo '</div>';

      echo '</div>';
    }
  }

  public function renderSlides($data) {

    echo '<div class="nc-slides-wrapper">';

    $i = 1;
    foreach ($data as $slide) {

      echo '<div class="nc-slide slideFadeIn">';
        $this->renderImage($slide->image);
        $this->renderCaption($slide->caption);
        $this->renderLink($slide->url);
      echo '</div>';

      $i++;
    }

    echo '</div>';
  }

  public function renderImage($fileName) {

    $image = Util::setMediaImageUrl($fileName);
    // echo '<img src="' . $image . '" alt="' . $image . '" />';
    echo '<div class="nc-slide-image" style="background-image: url(' . $image . ')"></div>';
  }

  public function renderLink($url) {

    if (@exists($url)) {

      echo '<a href="' . Util::parseLink($url) . '" class="nc-slide-link"></a>';
    }
  }

  public function renderCaption($caption) {

    if (@exists($caption)) {

      echo '<div class="nc-slide-caption">' . nl2br($caption) . '</div>';
    }
  }

  public function renderBulletsNavigation($items) {

    echo '<div class="nc-slider-bullets">';

    $itemsTotal = count($items);

    for ($i = 0; $i < $itemsTotal; $i++) {

      $active = (int)$i === 0 ? ' active' : '';

      echo '<a href="#" class="nc-slider-bullet' . $active . '"></a>';
    }

    echo '</div>';
  }

  public function renderArrowsNavigation()  {
    echo '<a href="#" class="nc-slider-navigation slide-prev" id="slidePrev"><i></i></a>';
    echo '<a href="#" class="nc-slider-navigation slide-next" id="slideNext"><i></i></a>';
  }

}

?>