<?php

/**
 * @file
 * Contains \Drupal\lift\Entity\LiftBlockViewBuilder.
 */

namespace Drupal\lift\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * @todo.
 */
class LiftBlockViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $block = $entity->block;
    $build = parent::view($entity, $view_mode, $langcode);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function viewMultiple(array $entities = [], $view_mode = 'full', $langcode = NULL) {
    return parent::viewMultiple($entities, $view_mode, $langcode);
    $build = [];
    foreach ($entities as $key => $entity) {
      $build[$key] = $this->view($entity, $view_mode, $langcode);
    }
    return $build;
  }

}
