<?php

/**
 * @file
 * Contains \Drupal\lift\Plugin\views\field\PluginID.
 */

namespace Drupal\lift\Plugin\views\field;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo.
 *
 * @ViewsField("lift_plugin_id")
 */
class PluginID extends FieldPluginBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $pluginManager;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Block\BlockManagerInterface $plugin_manager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BlockManagerInterface $plugin_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->pluginManager = $plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.block')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(ResultRow $values, $field = NULL) {
    $value = parent::getValue($values, $field);
    return $this->pluginManager->getDefinition($value)['admin_label'];
  }

}
