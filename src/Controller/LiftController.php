<?php

/**
 * @file
 * Contains \Drupal\lift\Controller\LiftController.
 */

namespace Drupal\lift\Controller;

use Drupal\Core\Entity\Controller\EntityViewController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for Lift routes.
 */
class LiftController extends EntityViewController {

  use StringTranslationTrait;

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

  /**
   * Route title callback.
   *
   * @param \Drupal\Core\Entity\EntityInterface $lift_block
   *   The Lift block entity.
   *
   * @return string
   *   The title for the Lift block edit form.
   */
  public function editLiftBlockTitle(EntityInterface $lift_block) {
    return $this->t('Edit %label Lift block', ['%label' => $lift_block->label()]);
  }

}
