<?php

/**
 * @file
 * Contains \Drupal\lift\Plugin\Field\FieldFormatter\LiftBlockFormatter.
 */

namespace Drupal\lift\Plugin\Field\FieldFormatter;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo.
 *
 * @FieldFormatter(
 *   id = "lift_blocks",
 *   label = @Translation("Lift blocks"),
 *   field_types = {
 *     "lift_blocks",
 *   }
 * )
 */
class LiftBlockFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $pluginManager;

  /**
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   * @param array $settings
   * @param string $label
   * @param string $view_mode
   * @param array $third_party_settings
   * @param \Drupal\Core\Block\BlockManagerInterface $plugin_manager
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, BlockManagerInterface $plugin_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->pluginManager = $plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('plugin.manager.block')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $data = [];
    foreach ($items as $item) {
      if ($plugin_id = $item->plugin_id) {
        $data[] = $this->pluginManager
          ->createInstance($plugin_id, $item->plugin_configuration)
          ->build();
      }
    }
    return $data;
  }

}
