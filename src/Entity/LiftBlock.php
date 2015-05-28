<?php

/**
 * @file
 * Contains \Drupal\lift\Entity\LiftBlock.
 */

namespace Drupal\lift\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * @todo.
 *
 * @ContentEntityType(
 *   id = "lift_block",
 *   label = @Translation("Lift block"),
 *   base_table = "lift_block",
 *   bundle_label = @Translation("Lift block type"),
 *   handlers = {
 *     "access" = "\Drupal\Core\Entity\EntityAccessControlHandler",
 *     "list_builder" = "\Drupal\lift\Entity\LiftBlockListBuilder",
 *     "view_builder" = "\Drupal\lift\Entity\LiftBlockViewBuilder",
 *     "views_data" = "\Drupal\lift\Entity\ListBlockViewsData",
 *     "storage" = "\Drupal\lift\Entity\LiftBlockStorage",
 *     "storage_schema" = "\Drupal\lift\Entity\LiftBlockStorageSchema",
 *     "form" = {
 *       "add" = "\Drupal\lift\Entity\Form\LiftBlockAddForm",
 *       "edit" = "\Drupal\lift\Entity\Form\LiftBlockEditForm",
 *     },
 *   },
 *   admin_permission = "administer lift",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "label",
 *   },
 *   field_ui_base_route = "entity.lift_block.collection",
 *   links = {
 *     "canonical" = "/lift_block/{lift_block}",
 *     "add-form" = "/admin/structure/lift/block",
 *     "edit-form" = "/admin/structure/lift/block/{lift_block}",
 *   }
 * )
 */
class LiftBlock extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $values, $entity_type, $bundle = FALSE, $translations = array()) {
    // Unserialize the values for fields that are stored in the base table,
    // because the entity storage does not do that.
    $property_names = ['block'];
    foreach ($property_names as $property_name) {
      if (isset($values[$property_name])) {
        foreach ($values[$property_name] as &$item_values) {
          $item_values['plugin_configuration'] = unserialize($item_values['plugin_configuration']);
        }
      }
    }
    parent::__construct($values, $entity_type, $bundle, $translations);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Lift block ID'))
      ->setReadOnly(TRUE);
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('Universally Unique ID'))
      ->setReadOnly(TRUE);
    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE);
    /*
    $fields['bundle'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Bundle'))
      ->setReadOnly(TRUE);
    //*/
    $fields['block'] = BaseFieldDefinition::create('lift_blocks')
      ->setLabel(t('Block'))
      ->setDisplayOptions('form', array(
        'type' => 'lift_blocks',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', array(
        'type' => 'lift_blocks',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
