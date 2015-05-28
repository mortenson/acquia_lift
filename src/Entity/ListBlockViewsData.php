<?php

/**
 * @file
 * Contains \Drupal\lift\Entity\ListBlockViewsData.
 */

namespace Drupal\lift\Entity;

use Drupal\views\EntityViewsData;

/**
 * @todo.
 */
class ListBlockViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();
    $data['lift_block']['block__plugin_id']['field']['id'] = 'lift_plugin_id';
    return $data;
  }

}
