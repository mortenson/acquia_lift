<?php

/**
 * @file
 * Contains \Drupal\lift\Entity\LiftBlockListBuilder.
 */

namespace Drupal\lift\Entity;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list builder for Lift blocks.
 */
class LiftBlockListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['plugin_id'] = $this->t('Block');
    $header['category'] = $this->t('Category');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label']['data'] = [
      '#type' => 'link',
      '#url' => $entity->urlInfo(),
      '#title' => $entity->label(),
    ];
    //$row['label'] = $this->getLabel($entity);
    $plugin_manager = \Drupal::service('plugin.manager.block');
    $definition = $plugin_manager->getDefinition($entity->block->plugin_id);
    $row['plugin_id'] = SafeMarkup::checkPlain($definition['admin_label']);
    $row['category'] = SafeMarkup::checkPlain($definition['category']);

    return $row + parent::buildRow($entity);
  }

}
