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
 * Provides a field widget for a Lift block plugin.
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
   * The block plugin manager.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $pluginManager;

  /**
   * Constructs a new LiftBlockWidget.
   *
   * @param array $plugin_id
   *   The plugin ID for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Block\BlockManagerInterface $plugin_manager
   *   The block plugin manager.
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

    $plugin_instance = $items->get($delta)->getContainedPluginInstance() ?: $this->pluginManager->createInstance(key($sorted_definitions));

    $options = [];
    foreach ($sorted_definitions as $plugin_id => $plugin_definition) {
      $category = SafeMarkup::checkPlain($plugin_definition['category']);
      $options[$category][$plugin_id] = $plugin_definition['admin_label'];
    }
    $element['plugin_id'] = [
      '#title' => $this->t('Block-to-be'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $plugin_instance->getPluginId(),
      '#ajax' => [
        'callback' => [$this, 'updateBlockPluginChoice'],
        'wrapper' => 'edit-plugin-configuration-wrapper',
        'method' => 'html',
      ],
    ];

    $element['plugin_configuration'] = $plugin_instance->buildConfigurationForm([], $form_state);
    $element['plugin_configuration']['#prefix'] = '<div id="edit-plugin-configuration-wrapper">';
    $element['plugin_configuration']['#suffix'] = '</div>';
    return $element;
  }

  /**
   * Switches the block plugin configuration form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The configuration form for the new block plugin.
   */
  public function updateBlockPluginChoice(array $form, FormStateInterface $form_state) {
    // Retrieve the new value for the plugin ID.
    $value = $form_state->getValue($form_state->getTriggeringElement()['#parents']);
    return $this->pluginManager
      ->createInstance($value)
      ->buildConfigurationForm([], $form_state);
  }

}
