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
    $render_array = parent::view($entity, $view_mode, $langcode);

    // After generating the render array for the entity, render it to be the
    // root of the page and return it as a response to bypass the rest of
    // Drupal's theming.
    return new Response($this->renderer->renderRoot($render_array));
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
