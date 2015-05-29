<?php

/**
 * @file
 * Contains \Drupal\lift\Entity\LiftBlockAccessControlHandler.
 */

namespace Drupal\lift\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the Lift block entity type.
 */
class LiftBlockAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    if ($operation === 'view') {
      return AccessResult::allowedIf($account->hasPermission('access lift blocks') || $account->hasPermission('administer lift'))
        ->cachePerPermissions()
        ->cacheUntilEntityChanges($entity);
    }

    return parent::checkAccess($entity, $operation, $langcode, $account);
  }

}
