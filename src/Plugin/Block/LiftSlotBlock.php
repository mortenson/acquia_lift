<?php

namespace Drupal\acquia_lift\Plugin\Block;

use Acquia\LiftClient\Entity\Slot;
use Acquia\LiftClient\Entity\Visibility;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the "Lift Slot" block.
 *
 * @Block(
 *   id = "lift_slot",
 *   admin_label = @Translation("Lift Slot"),
 *   category = @Translation("Lift")
 * )
 */
class LiftSlotBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'lift_slot_id' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['lift_existing_slot'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use existing slot'),
      '#default_value' => !empty($this->configuration['lift_slot_id']),
    ];

    $form['lift_new_slot'] = [
      '#type' => 'container',
      '#states' => [
        'invisible' => [
          ':input[name*="lift_existing_slot"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['lift_new_slot']['lift_slot_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('New Slot label'),
      '#states' => [
        'required' => [
          ':input[name*="lift_existing_slot"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['lift_new_slot']['lift_slot_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('New Slot description'),
    ];

    $form['lift_slot_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Existing Slot ID'),
      '#states' => [
        'invisible' => [
          ':input[name*="lift_existing_slot"]' => ['checked' => FALSE],
        ],
        'required' => [
          ':input[name*="lift_existing_slot"]' => ['checked' => TRUE],
        ],
      ],
      '#default_value' => $this->configuration['lift_slot_id'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    if (!$form_state->getValue('lift_existing_slot') && empty($form_state->getValue(['lift_new_slot', 'lift_slot_label']))) {
      $form_state->setErrorByName('lift_slot_label', 'The slot label is required.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Create a new Slot if an existing ID was not passed.
    if (!$form_state->getValue('lift_existing_slot')) {
      $slot = new Slot();
      $slot_id = \Drupal::service('uuid')->generate();
      $slot->setLabel($form_state->getValue(['lift_new_slot', 'lift_slot_label']));
      $slot->setDescription($form_state->getValue(['lift_new_slot', 'lift_slot_description']));
      $slot->setId($slot_id);
      $slot->setStatus(TRUE);

      // @todo Make this configurable.
      $visibility = new Visibility();
      $visibility->setCondition('show');
      $visibility->setPages(['*']);
      $slot->setVisibility($visibility);

      // @todo Include acquia/lift-sdk-php in this module and write similar
      // function to what as_lift provides: https://github.com/acquia/demo_framework/blob/6193e55bf901189263fbd3091ddad478ddee257f/modules/private/acquia_secret/as_lift/as_lift.module#L300
      $client = _as_lift_get_client();
      $slot_manager = $client->getSlotManager();
      $slot = $slot_manager->add($slot);
      $this->configuration['lift_slot_id'] = $slot->getId();
    }
    else {
      $this->configuration['lift_slot_id'] = $form_state->getValue('lift_slot_id');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $lift_slot_id = htmlspecialchars($this->configuration['lift_slot_id']);
    // @todo Allow users to define custom CSS stylesheets and use the
    // data-lift-css attribute.
    $build = [
      '#markup' => '<div data-lift-slot="' . $lift_slot_id . '"></div>',
      '#allowed_tags' => ['div'],
    ];
    return $build;
  }

}
