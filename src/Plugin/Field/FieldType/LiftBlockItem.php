<?php

/**
 * @file
 * Contains \Drupal\lift\Plugin\Field\FieldType\LiftBlockItem.
 */

namespace Drupal\lift\Plugin\Field\FieldType;

use Drupal\payment\Plugin\Field\FieldType\PluginBagItemBase;

/**
 * @todo.
 *
 * @FieldType(
 *   id = "lift_blocks",
 *   label = @Translation("Lift block plugins")
 * )
 */
class LiftBlockItem extends PluginBagItemBase {

  /**
   * {@inheritdoc}
   */
  public function getPluginManager() {
    return \Drupal::service('plugin.manager.block');
  }

}
