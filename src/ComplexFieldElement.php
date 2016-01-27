<?php

/**
 * @file
 * Contains \Drupal\complex_field\ComplexFieldElement.
 */

namespace Drupal\complex_field;

use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;

class ComplexFieldElement {

  /**
   * @var string $name
   * The machine name to use for this element's form key and storage.
   */
  protected $name;

  /**
   * @var string $title
   * The user-facing text for this subelement.
   */
  protected $title;

  /**
   * @var DataDefinitionInterface[] $propertyDefinitions
   * An array of DataDefinitionInterface objects as returned by FieldItemInterface::propertyDefinitions().
   */
  protected $propertyDefinitions;

  /**
   * @var FieldTypePluginManagerInterface $plugin_manager
   * Holds a plugin manager that we can use to lookup field type plugin information.
   */
  protected $pluginManager;

  /**
   * Gets an instance of the Field Type plugin manager.
   *
   * @todo Refactor this so that we're not calling \Drupal::service().
   *
   * @return FieldTypePluginManagerInterface
   * A field type plugin manager.
   */
  protected function getPluginManager() {
    if (!is_object($this->pluginManager)) {
      $this->pluginManager = \Drupal::service('plugin.manager.field.field_type');
    }

    return $this->pluginManager;
  }

  /**
   * Create an instance of this class pre-populated with data from the given field type.
   *
   * @param string $plugin_id
   * A plugin ID for a field type.
   */
  public function fromFieldTypePluginId($plugin_id) {
    $plugin_manager = $this->getPluginManager();

    $plugin_class = $plugin_manager->getPluginClass($plugin_id);



    $this->setPropertyDefinitions(call_user_func("{$plugin_class}::propertyDefinitions"));

  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param string $title
   */
  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  /**
   * @return \Drupal\Core\TypedData\DataDefinitionInterface[]
   */
  public function getPropertyDefinitions() {
    return $this->propertyDefinitions;
  }

  /**
   * @param \Drupal\Core\TypedData\DataDefinitionInterface[] $propertyDefinitions
   */
  public function setPropertyDefinitions($propertyDefinitions) {
    $this->propertyDefinitions = $propertyDefinitions;
  }

//
//  /**
//   * @var string $name
//   * Contains the machine name for this element.
//   */
//  protected $name;
//
//  /**
//   * @var string $title
//   * Contains the title that will be displayed to the user for this element.
//   */
//  protected $title;
//
//  /**
//   * @var string $type
//   * Contains the form API element type for this element.
//   */
//  protected $type;
//
//  /**
//   * @var string $storageType
//   * Contains the name of the data type that this element represents.
//   */
//  protected $storageType;
//
//  /**
//   * @var bool $required
//   * Whether or not this element is required.
//   */
//  protected $required;
//
//  /**
//   * @return string
//   */
//  public function getName() {
//    return $this->name;
//  }
//
//  /**
//   * @param string $name
//   * @return ComplexFieldElement
//   */
//  public function setName($name) {
//    $this->name = $name;
//    return $this;
//  }
//
//  /**
//   * @return string
//   */
//  public function getTitle() {
//    return $this->title;
//  }
//
//  /**
//   * @param string $title
//   * @return ComplexFieldElement
//   */
//  public function setTitle($title) {
//    $this->title = $title;
//    return $this;
//  }
//
//  /**
//   * @return string
//   */
//  public function getType() {
//    return $this->type;
//  }
//
//  /**
//   * @param string $type
//   * @return ComplexFieldElement
//   */
//  public function setType($type) {
//    $this->type = $type;
//    return $this;
//  }
//
//  /**
//   * @return string
//   */
//  public function getStorageType() {
//    return $this->storageType;
//  }
//
//  /**
//   * @param string $storageType
//   * @return ComplexFieldElement
//   */
//  public function setStorageType($storageType) {
//    $this->storageType = $storageType;
//    return $this;
//  }
//
//  /**
//   * @return boolean
//   */
//  public function isRequired() {
//    return $this->required;
//  }
//
//  /**
//   * @param boolean $required
//   */
//  public function setRequired($required) {
//    $this->required = $required;
//  }

}
