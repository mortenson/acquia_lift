<?php

/**
 * @file
 * Contains \Drupal\lift\Controller\LiftController.
 */

namespace Drupal\lift\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * Returns responses for Lift routes.
 */
class LiftController extends ControllerBase {

  /**
   * Renders a given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be viewed.
   *
   * @return array
   *   Either a render array for this entity, or an empty array if this entity
   *   type does not provide a view builder.
   */
  public function view(EntityInterface $entity) {
    $entity_manager = $this->entityManager();
    $entity_type_id = $entity->getEntityTypeId();

    $return = [];
    if ($entity_manager->hasHandler($entity_type_id, 'view_builder')) {
      $return = $entity_manager
        ->getViewBuilder($entity_type_id)
        ->view($entity);
    }
    return $return;
  }

}
