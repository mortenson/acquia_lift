<?php

/**
 * @file
 * Contains \Drupal\lift\Plugin\DataType\PluginInstance.
 */

namespace Drupal\lift\Plugin\DataType;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\TypedData\TypedData;

/**
 * Provides a plugin instance data type.
 *
 * @DataType(
 *   id = "lift_plugin_instance",
 *   label = @Translation("Plugin instance")
 * )
 */
class PluginInstance extends TypedData {

  /**
   * The plugin instance.
   *
   * @var \Drupal\Component\Plugin\PluginInspectionInterface
   */
  protected $value;

  /**
   * {@inheritdoc}
   */
  public function setValue($value, $notify = TRUE) {
    if (!$value instanceof PluginInspectionInterface) {
      $value = NULL;
    }
    parent::setValue($value, $notify);
  }

  /**
   * {@inheritdoc}
   */
  public function __clone() {
    if ($this->getValue()) {
      $this->setValue(clone $this->value);
    }
  }

}
