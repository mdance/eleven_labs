<?php

namespace Drupal\eleven_labs;

use Drupal\Component\Utility\NestedArray;

/**
 * Provides the NestedData class.
 */
class NestedData extends NestedArray {

  /**
   * {@inheritDoc}
   */
  public static function &getValue(&$array, $parents, &$key_exists = NULL) {
    $ref = &$array;

    if (!is_array($parents)) {
      $parents = [
        $parents,
      ];
    }

    foreach ($parents as $parent) {
      if (is_object($ref) && property_exists($ref, $parent)) {
        $ref = &$ref->$parent;
      }
      elseif (is_array($ref) && (isset($ref[$parent]) || array_key_exists($parent, $ref))) {
        $ref = &$ref[$parent];
      }
      else {
        $key_exists = FALSE;
        $null = NULL;
        return $null;
      }
    }

    $key_exists = TRUE;

    return $ref;
  }

}
