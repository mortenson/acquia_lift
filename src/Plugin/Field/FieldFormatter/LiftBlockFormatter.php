<?php

/**
 * @file
 * Contains \Drupal\lift\Plugin\Field\FieldFormatter\LiftBlockFormatter.
 */

namespace Drupal\lift\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Provides a field formatter for a Lift block plugin.
 *
 * @FieldFormatter(
 *   id = "lift_blocks",
 *   label = @Translation("Lift blocks"),
 *   field_types = {
 *     "lift_blocks",
 *   }
 * )
 */
class LiftBlockFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $data = [];
    foreach ($items as $item) {
      $data[] = $item->getContainedPluginInstance()->build();
    }
    return $data;
  }

}
