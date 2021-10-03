<?php

class MenuView extends MainView {

  private $data;

  public function __construct($data) {
    parent::__construct();

    if (@exists($data)) {
      $this->data = $data;
    }
  }


  public function displayMenu($elementId = null) {

    if (!@exists($elementId)) $elementId = 'mainMenu';

    if (@exists($this->data)) {

      $this->renderMenuOpener();

      $currentUrl = urldecode($_SERVER['REQUEST_URI']);

      echo '<div id="' . $elementId . '" class="main-menu clearfix">';
        $this->renderMenuItems($this->data, $currentUrl);
      echo '</div>';
    }
  }


  private function renderMenuOpener() {

    echo '<div class="menu-opener-wrapper">';
      echo '<button id="menuOpener">';
        echo '<span class="bar"></span>';
        echo '<span class="bar"></span>';
        echo '<span class="bar"></span>';
      echo '</button>';
    echo '</div>';
  }


  private function renderMenuItems($items, $currentUrl, $dropdownClass = null) {

    if (!exists($dropdownClass)) $dropdownClass = '';

    echo '<ul class="clearfix ' . $dropdownClass . '">';
    foreach ($items as $item) {

      //$activeClass = (endsWith($item['target'], $currentUrl)) ? ' active' : '';
      $activeClass = $this->setActiveClass($item['target']);

      $parentClass = (int)$item['parent_id'] === 0 ? 'first-level' : '';

      $parentLinkClass = '';
      $dropdownClass = '';

      if (@exists($item['children'])) {

        $parentClass .= ' dropdown-parent';
        $parentLinkClass = 'parent-link';
        $dropdownClass = 'dropdown';

        if ((int)$item['parent_id'] === 0) {
          $parentClass .= ' first-parent';
          $parentLinkClass .= ' first-parent-link';
          $dropdownClass .= ' first-child';
        }
      }

      if ($parentClass !== '') $parentClass = ' class="' . $parentClass . '"';

      echo '<li' . $parentClass . '>' . $this->renderMenuItemLink($item, $activeClass, $parentLinkClass);

      if (@exists($item['children'])) {

        if (@exists($item['children']) > 0) $this->renderMenuItems($item['children'], $currentUrl, $dropdownClass);
      }
      echo '</li>';
    }
    echo '</ul>';
  }


  private function renderMenuItemLink($item, $activeClass, $parentLinkClass) {

    $elementClass = $parentLinkClass . $activeClass;
    $elemClass = ((string)$elementClass !== '') ? 'class="' . $elementClass . '"' : '';

    $dropdownIcon = strpos($parentLinkClass, 'parent-link') !== false ? '<i class="fas fa-angle-down"></i>' : '';

    $target = (int)$item['type'] === (int)Conf::get('menu_item_type_id')['separator'] ? '#' : Util::parseLink($item['target']);

    $link = '<a href="' . $target . '" ' . $elemClass . '>' . $item['name'] . ' ' . $dropdownIcon . '</a>';

    if ((string)$parentLinkClass !== '') {
      $link .= '<a href="#" class="dropdown-opener">';
        $link .= '<i class="fas fa-chevron-down element-center"></i>';
      $link .= '</a>';
    }

    return $link;
  }


  private function setActiveClass($target) {

    $currentUrl = urldecode($_SERVER['REQUEST_URI']);

    if (Conf::get('base') !== '') {
      $currentUrl = str_replace(Conf::get('base'), '', $currentUrl);
    }

    $currentUrl = trim($currentUrl, '/');

    $target = trim(str_replace(Conf::get('url'), '', $target), '/');

    return $target === $currentUrl ? ' active' : '';
  }
}

?>