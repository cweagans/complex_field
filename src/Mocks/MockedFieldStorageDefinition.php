<?php

/**
 * @file
 * Contains \Drupal\complex_field\Mocks\MockedFieldStorageDefinition.
 */

namespace Drupal\complex_field\Mocks;

use Drupal\complex_field\Exception\MissingStorageSettingException;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

class MockedFieldStorageDefinition implements FieldStorageDefinitionInterface {

  /**
   * An array of subfield settings.
   *
   * @var array $settings
   */
  protected $settings;

  /**
   * {@inheritdoc}
   */
  public function getSetting($setting_name) {
    if (isset($this->settings[$setting_name])) {
      return $this->settings[$setting_name];
    }

    $message = "{$setting_name} has not been set yet, but was requested by one of the sub-elements of a Complex Field.";
    throw new MissingStorageSettingException($message);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettings() {
    return $this->settings;
  }

  /**
   * Configure settings for subfields that this object will get passed to.
   *
   * @param $settings
   */
  public function setSettings($settings) {
    $this->settings = $settings;
  }

  /**
   * Configure a single setting for subfields that this object will get passed to.
   *
   * @param $name
   * @param $value
   */
  public function setSetting($name, $value) {
    $this->settings[$name] = $value;
  }


  // These are just to satisfy the interface. The core field types only use above
  // for the things that we care about.
  public function getName() {}
  public function getLabel() {}
  public function getTargetEntityTypeId() {}
  public function getCacheContexts() {}
  public function getCacheTags() {}
  public function getCacheMaxAge() {}
  public function getType() {}
  public function isTranslatable() {}
  public function setTranslatable($translatable) {}
  public function isRevisionable() {}
  public function isQueryable() {}
  public function getDescription() {}
  public function getOptionsProvider($property_name, FieldableEntityInterface $entity) {}
  public function isMultiple() {}
  public function getCardinality() {}
  public function getPropertyDefinition($name) {}
  public function getPropertyDefinitions() {}
  public function getPropertyNames() {}
  public function getMainPropertyName() {}
  public function getSchema() {}
  public function getColumns() {}
  public function getConstraints() {}
  public function getConstraint($constraint_name) {}
  public function getProvider() {}
  public function hasCustomStorage() {}
  public function isBaseField() {}
  public function getUniqueStorageIdentifier() {}
}
