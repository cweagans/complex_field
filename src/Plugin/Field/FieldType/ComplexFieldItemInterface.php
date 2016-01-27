<?php

/**
 * @file
 * Contains \Drupal\complex_field\Plugin\Field\FieldType\ComplexFieldItemInterface.
 */

namespace Drupal\complex_field\Plugin\Field\FieldType;

use Drupal\complex_field\Exception\NotImplementedException;

interface ComplexFieldItemInterface {

  /**
   * Return the list of fields this Complex Field should use.
   *
   * Other FieldItemInterface outputs are automatically generated based on this list.
   *
   * WARNING: After this field type has been created, DO NOT change the output
   * of this function. Severe data loss and unimaginable sadness will follow.
   *
   * @throws NotImplementedException
   *   Thrown when the method is not overridden by the child class.
   * @return array
   *   An array where the keys are the name of the subelement and the values are the
   *   field type plugin IDs to use for the subelement.
   */
  public static function getSubelements();

}
