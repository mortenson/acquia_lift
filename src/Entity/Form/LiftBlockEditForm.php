<?php

/**
 * @file
 * Contains \Drupal\lift\Entity\Form\LiftBlockEditForm.
 */

namespace Drupal\lift\Entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * @todo.
 */
class LiftBlockEditForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    drupal_set_message($this->t('The %label Lift block has been updated.', ['%label' => $this->entity->label()]));
    $form_state->setRedirect('entity.lift_block.collection');
  }

}
