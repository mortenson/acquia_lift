<?php

/**
 * @file
 * Contains \Drupal\lift\Plugin\Field\FieldWidget\LiftBlockWidget.
 */

namespace Drupal\lift\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @todo.
 *
 * @FieldWidget(
 *   id = "lift_blocks",
 *   label = @Translation("Lift block"),
 *   field_types = {
 *     "lift_blocks"
 *   }
 * )
 */
class LiftBlockWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $pluginManager;

  /**
   * @param array $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   * @param array $settings
   * @param array $third_party_settings
   * @param \Drupal\Core\Block\BlockManagerInterface $plugin_manager
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, BlockManagerInterface $plugin_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
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
      $configuration['third_party_settings'],
      $container->get('plugin.manager.block')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $definitions = $this->pluginManager->getDefinitions();
    $sorted_definitions = $this->pluginManager->getSortedDefinitions($definitions);
    $default_plugin_id = $items->plugin_id ?: key($sorted_definitions);
    $options = [];
    foreach ($sorted_definitions as $plugin_id => $plugin_definition) {
      $category = SafeMarkup::checkPlain($plugin_definition['category']);
      $options[$category][$plugin_id] = $plugin_definition['admin_label'];
    }
    $element['choices'] = [
      '#title' => $this->t('Block-to-be'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $default_plugin_id,
      '#ajax' => [
        'callback' => [$this, 'updateBlockPluginChoice'],
        'wrapper' => 'edit-config-type-wrapper',
        'method' => 'html',
      ],
    ];
    $default_plugin = $this->pluginManager->createInstance($default_plugin_id);
    $element['block_configuration'] = $default_plugin->buildConfigurationForm([], $form_state);
    $element['block_configuration']['#prefix'] = '<div id="edit-config-type-wrapper">';
    $element['block_configuration']['#suffix'] = '</div>';
    return $element;
  }

  /**
   * @todo.
   */
  public function updateBlockPluginChoice($form, FormStateInterface $form_state) {
    $value = $form_state->getValue($form_state->getTriggeringElement()['#parents']);
    return $this->pluginManager
      ->createInstance($value)
      ->buildConfigurationForm([], $form_state);
  }

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $data = [];
    $data[] = [
      'plugin_id' => $values[0]['choices'],
      'plugin_configuration' => $values[0]['block_configuration'],
    ];
    return $data;
  }
}
