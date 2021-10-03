<?php

class FooterView extends MainView {


   public function __construct() {
      parent::__construct();
   }


   public function renderLoader() {

      echo '<div id="overlay" class="page-overlay"></div>';
      echo '<div id="loader">';
         echo '<div class="loader-icon"><img src="' . Conf::get('css_img_url') . '/loader.png" /></div>';
      echo '</div>';
   }
}
?>