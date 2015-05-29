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

/**
 * Returns responses for Lift routes.
 */
class LiftController extends EntityViewController {

  use StringTranslationTrait;

  /**
   * Returns a JSON representing the rendered entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being rendered.
   * @param string $view_mode
   *   The view mode that should be used to display the entity.
   * @param string $langcode
   *   For which language the entity should be rendered.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON representation of the rendered entity, with the following keys:
   *   - body: The markup of the entity itself.
   *   - styles: The markup for any CSS associated with this entity.
   *   - scripts: The markup for any JS belonging in the <head> element that is
   *     associated with this entity.
   *   - scripts_bottom: The markup for any JS belonging in the bottom of the
   *     <body> element that is associated with this entity.
   *   - head: The markup for any other elements to be in <head>.
   */
  public function getRenderedEntity(EntityInterface $entity, $view_mode, $langcode) {
    $render_array = $this->view($entity, $view_mode, $langcode);

    $result['body'] = $this->renderer->render($render_array);

    return new JsonResponse($result);
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
  public function getEntityData(EntityInterface $entity) {
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
