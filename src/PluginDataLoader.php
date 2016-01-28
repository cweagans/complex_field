<?php

/**
 * @file
 * Contains \Drupal\complex_field\PluginDataLoader.
 */

namespace Drupal\complex_field;

use Drupal\complex_field\Exception\NotImplementedException;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Field\WidgetPluginManager;

class PluginDataLoader {

  /**
   * @var FieldTypePluginManagerInterface $fieldTypePluginManager
   * Holds a plugin manager that we can use to lookup field type plugin information.
   */
  protected $fieldTypePluginManager;

  /**
   * @var WidgetPluginManager $widgetPluginManager
   * Holds a plugin manager that we can use to get field widget plugin information.
   */
//  protected $widgetPluginManager;

  /**
   * PluginDataLoader constructor.
   *
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_plugin_manager
   *
   * @todo Pass in the WidgetPluginManager service.
   */
  public function __construct(FieldTypePluginManagerInterface $field_type_plugin_manager) {
    $this->fieldTypePluginManager = $field_type_plugin_manager;
//    $this->widgetPluginManager = $widget_plugin_manager;
  }

  /**
   * Get an array of property definitions from a given plugin.
   *
   * @param string $plugin_id
   *   A plugin ID for a field type.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface[]
   *   An array of property definitions of contained properties, keyed by
   *   property name.
   */
  public function getFieldTypePropertyDefinitions($plugin_id, FieldStorageDefinitionInterface $field_definition) {
    return $this->getFieldTypeData("propertyDefinitions", $plugin_id, $field_definition);
  }

  /**
   * Get the schema definition for a given plugin.
   *
   * @param string $plugin_id
   *   A plugin ID for a field type.
   *
   * @return array[]
   *
   * @see Drupal\Core\Field\FieldItemInterface::schema()
   *   Outputs the data we're returning here.
   */
  public function getFieldTypeSchemaDefinition($plugin_id, FieldStorageDefinitionInterface $field_definition) {
    return $this->getFieldTypeData("schema", $plugin_id, $field_definition);
  }

  /**
   * Get the name of the main property of a field type plugin.
   *
   * @param string $plugin_id
   *   A plugin ID for a field type
   *
   * @return string
   *   The name of the property
   */
  public function getFieldTypeMainProperty($plugin_id) {
    $plugin_class = $this->fieldTypePluginManager->getPluginClass($plugin_id);
    $main_property = call_user_func([$plugin_class, 'mainPropertyName']);
    return $main_property;
  }

  /**
   * Get data from a field type plugin.
   *
   * @param $type
   *   One of "schema" or "propertyDefinitions"
   * @param $plugin_id
   *   A plugin ID for a field type.
   * @param \Drupal\Core\Field\FieldStorageDefinitionInterface $field_storage_definition
   *
   * @return mixed
   */
  protected function getFieldTypeData($type, $plugin_id, FieldStorageDefinitionInterface $field_storage_definition) {
    // Get the name of the class from the plugin ID.
    $plugin_class = $this->fieldTypePluginManager->getPluginClass($plugin_id);

    // Bail out early if an unimplemented type is requested.
    $allowed_types = ["schema", "propertyDefinitions"];
    if (!in_array($type, $allowed_types)) {
      throw new NotImplementedException("Data type {$type}");
    }

    // Call the static method on the plugin class.
    return call_user_func("{$plugin_class}::{$type}", $field_storage_definition);
  }

  // @todo Add support for pulling widget forms directly from field widget plugins.
//  public function getWidgetForm($plugin_id, $item) {
//
//  }
}
