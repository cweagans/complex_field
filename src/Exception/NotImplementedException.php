<?php

/**
 * @file
 * Contains Drupal\complex_field\Exception\NotImplementedException.
 */

namespace Drupal\complex_field\Exception;

/**
 * Class NotImplementedException
 *
 * @package Drupal\complex_field\Exception
 */
class NotImplementedException extends \RuntimeException {

  /**
   * NotImplementedException constructor.
   *
   * @param string $message
   *   The name of the unimplemented method.
   * @param int $code
   * @param \Exception $previous
   */
  public function __construct($message, $code = 0, \Exception $previous = null) {
    $message = $message . " is not implemented!";
    parent::__construct($message, $code, $previous);
  }

}
