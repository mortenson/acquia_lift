<?php

/**
 * @file
 * Contains \Drupal\lift\Controller\LiftController.
 */

namespace Drupal\lift\Controller;

use Drupal\Core\Entity\Controller\EntityViewController;
use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for Lift routes.
 */
class LiftController extends EntityViewController {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $return = '';
    // @todo Remove this check once https://www.drupal.org/node/2495947 is in.
    if ($this->entityManager->hasHandler($entity->getEntityTypeId(), 'view_builder')) {
      $render_array = parent::view($entity, $view_mode, $langcode);
      // After generating the render array for the entity, render it to be the
      // root of the page.
      $return = $this->renderer->renderRoot($render_array);
    }
    // Directly set it as a response to bypass the rest of Drupal's theming.
    return new Response($return);
  }

  /**
   * Returns a JSON representation of the entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being viewed.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON representation of the entity.
   */
  public function viewJson(EntityInterface $entity) {
    return new JsonResponse($entity->toArray());
  }

}
