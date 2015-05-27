<?php

/**
 * @file
 * Contains \Drupal\lift\Controller\LiftController.
 */

namespace Drupal\lift\Controller;

use Drupal\Core\Entity\Controller\EntityViewController;
use Drupal\Core\Entity\EntityInterface;

/**
 * Returns responses for Lift routes.
 */
class LiftController extends EntityViewController {

  /**
   * {@inheritdoc}
   *
   * @todo Remove once https://www.drupal.org/node/2495947 is in.
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $return = [];
    if ($this->entityManager->hasHandler($entity->getEntityTypeId(), 'view_builder')) {
      $return = parent::view($entity, $view_mode, $langcode);
    }
    return $return;
  }

}
