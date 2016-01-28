<?php

/**
 * @file
 * Contains \Drupal\complex_field\Plugin\Field\FieldFormatter\ComplexFieldFormatter.
 */

namespace Drupal\complex_field\Plugin\Field\FieldFormatter;

use Drupal\complex_field\Exception\NotImplementedException;
use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Class ComplexFieldFormatterBase
 *
 * Provides a default formatter implementation for core field types included in
 * a Complex Field. No extension of this class is necessary to get basic output,
 * although you may want to consider overriding the default behaviors if you care
 * about how your content looks.
 *
 * @package Drupal\complex_field\Plugin\Field\FieldFormatter
 *
 * @FieldFormatter(
 *   id = "complex_field_formatter",
 *   label = @Translation("Complex Field formatter")
 * )
 */
class ComplexFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $subelements = static::getFieldItemSubelements();

    foreach ($items as $delta => $item) {
      $elements[$delta] = $this->viewValue($item, $subelements);
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // If the field item extends ComplexFieldItemBase, then this formatter applies.
    return is_subclass_of($field_definition->getClass(), '\Drupal\complex_field\Plugin\Field\FieldType\ComplexFieldItemBase');
  }

  /**
   * Returns whatever the base class gets.
   */
  protected function getFieldItemSubelements() {
    $field_definition_class = $this->fieldDefinition->getItemDefinition()->getClass();
    return call_user_func([$field_definition_class, 'getSubelements']);
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   * @param array $subelements
   *   An array of subelements as configured in a ComplexFieldType class.
   *
   * @return array
   *   A render array representing all of the values of the current field delta.
   */
  protected function viewValue(FieldItemInterface $item, array $subelements) {
    $output = [];

    foreach ($subelements as $name => $subelement) {
      $output[$name] = $this->renderSubelement($item, $name, $subelements);

      // Slap a title on any #markup elements.
      // @todo Make this nice.
      if (isset($output[$name]['#markup'])) {
        $output[$name]['#prefix'] = '<div class="field__label">' . Html::escape($subelement['label']) . '</div>';
      }

      // If it's something else that has a type explicitly set, we can use #title
      if (isset($output[$name]['#type'])) {
        $output[$name]['#title'] = $subelement['label'];
      }
    }

    return $output;
  }

  /**
   * Renders a subelement in the way that requires the least effort.
   *
   * @param FieldItemInterface $item
   *   A field item to pull the subelement data from.
   * @param string $subelement_name
   *   The name of the subelement config to use
   * @param array $subelements
   *   An array of subelements as configured in a ComplexFieldType class.
   *
   * @throws NotImplementedException
   *   When a render function is not available for a given subelement type.
   *   Implementers will have to provide their own render function in this case.
   *
   * @return string
   *   Markup for the subelement.
   */
  protected function renderSubelement($item, $subelement_name, $subelements) {

    // Get the method name from the plugin type.
    $plugin = $subelements[$subelement_name]['plugin'];
    $method_name = $this->typeToMethodName($plugin);

    // @todo Use DI for this.
    $plugin_main_property_name = \Drupal::service('complex_field.plugin_data_loader')
      ->getFieldTypeMainProperty($plugin);

    // If we have a render method specific to this type, use it.
    if (method_exists($this, $method_name)) {
      return $this->{$method_name}($item, $subelement_name, $plugin_main_property_name);
    }

    // If not, fall back to the simple render function.
    try {
      return $this->_renderSimpleValue($item, $subelement_name, $plugin_main_property_name);
    }
    catch (\LogicException $e) {
      // Simple render function isn't applicable. No need to do anything here -
      // we just want to continue on.
    }

    // As a last resort, throw an exception.
    throw new NotImplementedException("Render method for {$plugin}");
  }

  /**
   * Convert a plugin type to a render method name.
   *
   * @param $type
   *   The name of the plugin to convert to a method name.
   * @return string
   *   The name of the method.
   */
  protected function typeToMethodName($type) {
    return "render" . str_replace(" ", "", ucwords(str_replace("_", " ", $type)));
  }

  /**
   * Renders any single value subelement where ::mainPropertyName() is set.
   *
   * @param FieldItemInterface $item
   *   A field item to pull the subelement data from.
   * @param string $subelement_name
   *   The name of the subelement config to use
   * @param string $plugin_main_property_name
   *   The name of the main property in the subelement plugin.
   *
   * @return array
   *   A render array for the subelement.
   */
  protected function _renderSimpleValue($item, $subelement_name, $plugin_main_property_name) {

    // If there is a main property (which is the default), just output that as a string.
    if (!is_null($plugin_main_property_name)) {
      $value_name = $subelement_name . '_' . $plugin_main_property_name;
      return ['#markup' => nl2br(Html::escape($item->{$value_name}))];
    }

    // Getting to this point should be very rare, and only means that a type-
    // specific render function must be defined.
    throw new \LogicException("No main property is set for {$subelement_name}");
  }

  /**
   * Renders a field item as text + format.
   *
   * @param FieldItemInterface $item
   */
  protected function _renderTextWithFormat($item, $subelement_name, $plugin_main_property_name) {
    $value_name = $subelement_name . '_value';
    $format_name = $subelement_name . '_format';
    if (isset($item->value) && isset($item->format)) {
      return [
        '#type' => 'processed_text',
        '#text' => $item->{$value_name},
        '#format' => $item->{$format_name},
        '#langcode' => $item->getLangcode(),
      ];
    }
  }

  /**
   * Renders a map subelement.
   *
   * @param FieldItemInterface $item
   *   A field item to pull the subelement data from.
   * @param string $subelement_name
   *   The name of the subelement config to use
   * @param array $subelements
   *   An array of subelements as configured in a ComplexFieldType class.
   *
   * @return array
   *   A render array for the subelement.
   */
  protected function renderMap($item, $subelement_name, $plugin_main_property_name) {
    // Meh. Implementers can deal with this, as it's usage should be pretty rare.
    throw new NotImplementedException("::renderMap()");
  }

  /**
   * Renders a text subelement.
   *
   * @param FieldItemInterface $item
   *   A field item to pull the subelement data from.
   * @param string $subelement_name
   *   The name of the subelement config to use
   * @param array $subelements
   *   An array of subelements as configured in a ComplexFieldType class.
   *
   * @return array
   *   A render array for the subelement.
   */
  protected function renderText($item, $subelement_name, $plugin_main_property_name) {
    return $this->_renderTextWithFormat($item, $subelement_name, $plugin_main_property_name);
  }

  /**
   * Renders a text_long subelement.
   *
   * @param FieldItemInterface $item
   *   A field item to pull the subelement data from.
   * @param string $subelement_name
   *   The name of the subelement config to use
   * @param array $subelements
   *   An array of subelements as configured in a ComplexFieldType class.
   *
   * @return array
   *   A render array for the subelement.
   */
  protected function renderTextLong($item, $subelement_name, $plugin_main_property_name) {
    return $this->_renderTextWithFormat($item, $subelement_name, $plugin_main_property_name);
  }

  /**
   * Renders a text_with_summary subelement.
   *
   * @param FieldItemInterface $item
   *   A field item to pull the subelement data from.
   * @param string $subelement_name
   *   The name of the subelement config to use
   * @param array $subelements
   *   An array of subelements as configured in a ComplexFieldType class.
   *
   * @return array
   *   A render array for the subelement.
   */
  protected function renderTextWithSummary($item, $subelement_name, $plugin_main_property_name) {
    return $this->_renderTextWithFormat($item, $subelement_name, $plugin_main_property_name);
  }

}
