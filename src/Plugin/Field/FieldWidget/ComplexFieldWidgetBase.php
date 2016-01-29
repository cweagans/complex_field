<?php

/**
 * @file
 * Contains \Drupal\complex_field\Plugin\Field\FieldWidget\ComplexFieldWidgetBase.
 */

namespace Drupal\complex_field\Plugin\Field\FieldWidget;

use Drupal\complex_field\Exception\NotImplementedException;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Default complex field widget.
 *
 * Implementers are responsible for providing their own form element function for
 * whatever data types their widget will be responsible for rendering. Extend this
 * class (don't specify the fields it applies to in the annotation - it's automatic),
 * and implement the render methods that the NotImplementedException will tell
 * you about when you enable your widget.
 */
class ComplexFieldWidgetBase extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // If the field item extends ComplexFieldItemBase, then this formatter applies.
    return is_subclass_of($field_definition->getClass(), '\Drupal\complex_field\Plugin\Field\FieldType\ComplexFieldItemBase');
  }

  /**
   * Returns whatever the ComplexFieldItem subclass for this field defines.
   */
  protected function getFieldItemSubelements() {
    $field_definition_class = $this->fieldDefinition->getItemDefinition()->getClass();
    return call_user_func("{$field_definition_class}::getSubelements");
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta];
    $subelements = $this->getFieldItemSubelements();

    // Group the fields.
    $element['#type'] = 'container';

    foreach ($subelements as $name => $config) {

      // Get the main property name for the subfield type.
      // @todo Use dependency injection here.
      $main_property = \Drupal::service('complex_field.plugin_data_loader')
        ->getFieldTypeMainProperty($config['plugin']);

      // $name_$main_property so that things will save correctly. Name of the form keys
      // has to match the property name that they get saved to.
      $element_name = $name . '_' . $main_property;
      $element[$element_name] = $this->getSubelementFormElement($item, $name, $subelements, $element_name);
    }

    return $element;
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
    return lcfirst(str_replace(" ", "", ucwords(str_replace("_", " ", $type)))) . "WidgetForm";
  }

  /**
   * Generate the form element for a subfield.
   *
   * @param FieldItemInterface $item
   *   The item to pull the subelement out of.
   * @param string $subelement_name
   *   The subelement name.
   * @param array $subelements
   *   The subelement configuration array.
   * @param string $element_name
   *   The name of the $item key where the value can be found.
   *
   * @return array
   *   The form item to add to the element form.
   *
   * @todo This should use the widget forms defined in FieldWidget plugins instead of panicking when there's no custom render function.
   */
  protected function getSubelementFormElement($item, $subelement_name, $subelements, $element_name) {
    // Get the method name from the plugin type.
    $plugin = $subelements[$subelement_name]['plugin'];
    $method_name = $this->typeToMethodName($plugin);

    // If we have a form method specific to this type, use it.
    if (method_exists($this, $method_name)) {
      return $this->{$method_name}($item, $subelement_name, $subelements, $element_name);
    }

    // If not, then try to get the form provided by the FieldWidget specified in $subelements.
    // @todo Implement this.

    // As a last resort, throw an exception.
    throw new NotImplementedException("Form element method for {$plugin} (::{$method_name})");
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    // @TODO: Detect and prune empty subelement values here? Or figure out why FieldItemBase isn't doing it properly?
    // Implementers will have to do this manually for now.
    return $values;
  }

}
