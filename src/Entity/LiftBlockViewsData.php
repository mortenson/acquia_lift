<?php

/**
 * @file
 * Contains \Drupal\lift\Entity\LiftBlockViewsData.
 */

namespace Drupal\lift\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for the Lift block entity type.
 */
class LiftBlockViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    $data['lift_block']['block__plugin_id']['field']['id'] = 'lift_plugin_id';
    return $data;
  }

}
