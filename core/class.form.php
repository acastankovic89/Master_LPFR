<?php

class Form {

  private static function setDataAttributes($attributes) {

    $dataAttributes = '';

    if(@exists($attributes['data'])) {

      foreach ($attributes['data'] as $attribute) {

        $dataAttributes .= ' data-' . $attribute['name'] . '=' . $attribute['value'];
      }
    }

    return $dataAttributes;
  }


  public static function label($label, $forName = null) {

    $for = @exists($forName) ? ' for="' . $forName . '"' : '';

    return '<label' . $for . '>' . $label . '</label>';
  }

  // $attributes = array('className', 'idName', 'disabled', 'required', 'placeholder')
  // $attributes['data'] -  array('data' => array(array('name' => 'id', 'value' => '1'), array('name' => 'toggle', 'value' => 'datepicker')))

  public static function field($type, $name, $value, $attributes = array()) {

    $className = @exists($attributes['className']) ? ' ' . $attributes['className'] : '';
    $elemId = @exists($attributes['idName']) ? 'id="' . $attributes['idName'] . '"' : '';
    $disabled = @exists($attributes['disabled']) ? ' disabled' : '';
    $required = @exists($attributes['required']) ? ' required' : '';
    $placeholder = @exists($attributes['placeholder']) ? ' placeholder="' . $attributes['placeholder'] . '"' : '';
    $dataWarning = @exists($attributes['required']) ? ' data-warning="' . Trans::get('required') . '"' : '';

    $dataAttributes = self::setDataAttributes($attributes);

    return '<input type="' . $type . '" 
                   name="' . $name . '" 
                   value="' . $value . '" 
                   class="form-field' . $className . $disabled . $required .'" 
                   ' . $elemId . ' 
                   ' . $disabled . $required . '
                   ' . $placeholder . '
                   ' . $dataAttributes . '
                   ' . $dataWarning . '
                   />';
  }


  public static function textarea($name, $value, $attributes = array()) {

    $className = @exists($attributes['className']) ? ' ' . $attributes['className'] : '';
    $elemId = @exists($attributes['idName']) ? 'id="' . $attributes['idName'] . '"' : '';
    $disabled = @exists($attributes['disabled']) ? ' disabled' : '';
    $required = @exists($attributes['required']) ? ' required' : '';
    $placeholder = @exists($attributes['placeholder']) ? ' placeholder="' . $attributes['placeholder'] . '"' : '';
    $dataWarning = @exists($attributes['required']) ? ' data-warning="' . Trans::get('required') . '"' : '';

    return '<textarea name="' . $name . '" 
                      class="form-field' . $className . $disabled . $required .'"
                      ' . $elemId . ' 
                      ' . $disabled . $required . '
                      ' . $placeholder . '
                      ' . $dataWarning . '
                      >' . $value . '</textarea>';
  }


  /*
      $options - array(
        array('value' => '', 'title' => ''),
        array('value' => '', 'title' => ''),
        array('value' => '', 'title' => '')
      )
  */
  public static function ncSelectAdmin($name, $label, $options, $selectedValue, $attributes = array()) {

    if(!@exists($label)) {
      $label = Trans::get('Choose') . '...';
    }

    $elemId = @exists($attributes['idName']) ? 'id="' . $attributes['idName'] . '"' : '';
    $wrapperId = @exists($attributes['wrapperIdName']) ? 'id="' . $attributes['wrapperIdName'] . '"' : '';
    $required = @exists($attributes['required']) ? ' required' : '';

    $html = '<div class="nc-selectbox"' . $wrapperId . '>';
      $html .= '<label>' . $label . '</label>';
      $html .= '<input type="text" class="form-field nc-select" />';
      $html .= '<input type="hidden" class="form-field nc-select-value' . $required . '" name="' . $name . '"' . $elemId . ' />';
      $html .= '<i class="fas fa-chevron-down"></i>';

      if(@exists($options)) {

        $html .= '<div class="nc-select-options">';

          foreach ($options as $option) {

            $selected = @exists($selectedValue) && $selectedValue == $option['value'] ? ' selected' : '';

            $html .= '<a href="#" data-value="' . $option['value'] . '"' . $selected . '>' . $option['title'] . '</a>';
          }
        $html .= '</div>';
      }
    $html .= '</div>';

    return $html;
  }


  /*
    $options - array(
      array('value' => '', 'title' => ''),
      array('value' => '', 'title' => ''),
      array('value' => '', 'title' => '')
    )
  */
  public static function ncSelect($name, $label, $options, $selectedValue, $attributes = array()) {

    $className = @exists($attributes['className']) ? ' ' . $attributes['className'] : '';
    $elemId = @exists($attributes['idName']) ? ' id="' . $attributes['idName'] . '"' : '';
    $labelId = @exists($attributes['idName']) ? ' for="' . $attributes['idName'] . '"' : '';
    $wrapperId = @exists($attributes['wrapperIdName']) ? ' id="' . $attributes['wrapperIdName'] . '"' : '';
    $required = @exists($attributes['required']) ? ' required' : '';
    $dataWarning = @exists($attributes['required']) ? ' data-warning="' . Trans::get('required') . '"' : '';

    $selected = array(
      'value' => '',
      'title' => '',
      'selectedOptionAttr' => ''
    );

    $titlePlaceholderClassName = '';
    $dataPlaceholder = '';
    if(@exists($attributes['placeholder'])) {
      $selected['title'] = $attributes['placeholder'];
      $titlePlaceholderClassName = ' placeholder';
      $dataPlaceholder = ' data-placeholder="' . $attributes['placeholder'] . '"';
    }

    if(@exists($selectedValue)) {
      foreach ($options as $option) {
        if($selectedValue == $option['value']) {
          $selected = array(
            'value' => $option['value'],
            'title' => $option['title'],
            'selectedOptionAttr' => ' selected'
          );
          $titlePlaceholderClassName = '';
        }
      }
    }

    $html = '<div class="nc-selectbox"' . $wrapperId . '>';
      $html .= '<input type="hidden" class="nc-selectbox-value' . $required . $className . '" name="' . $name . '"' . $elemId . ' value="' . $selected['value']  . '"' . $dataWarning . $dataPlaceholder . '/>';
      if(@exists($label)) $html .= '<label' . $labelId . '>' . $label . '</label>';
      $html .= '<div class="nc-selectbox-header">';
        $html .= '<span class="nc-selectbox-title' . $titlePlaceholderClassName . '"' . $dataPlaceholder . '>' . $selected['title']  . '</span>';
        $html .= '<i class="fas fa-chevron-down"></i>';
      $html .= '</div>';
      $html .= '<div class="nc-selectbox-body">';
      if(@exists($options)) {
        foreach ($options as $option) {
          $html .= '<button type="button" data-value="' . $option['value'] . '"' . $option['selectedOptionAttr'] . '>' . $option['title'] . '</button>';
        }
      }
      $html .= '</div>';
    $html .= '</div>';

    return $html;
  }

  public static function checkbox($name, $value, $attributes = array()) {

    $className = @exists($attributes['className']) ? ' ' . $attributes['className'] : '';
    $elemId = @exists($attributes['idName']) ? 'id="' . $attributes['idName'] . '"' : '';
    $disabled = @exists($attributes['disabled']) ? ' disabled' : '';
    $required = @exists($attributes['required']) ? ' required' : '';
    $checked = (int)$value === 1 ? ' checked' : '';

    $html = '<div class="checkbox-wrapper">';
      $html .= '<input type="checkbox" 
                   class="form-checkbox' . $className . $disabled . $required .'"
                   ' . $checked . '
                   ' . $disabled . $required . '  />';

      $html .= '<input type="hidden" 
                   name="' . $name . '" 
                   value="' . $value . '" 
                   ' . $elemId . ' 
                   class="form-checkbox-value" />';
    $html .= '</div>';

    return $html;
  }
}

?>