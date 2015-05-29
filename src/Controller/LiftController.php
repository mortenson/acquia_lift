<?php

/**
 * @file
 * Contains \Drupal\lift\Controller\LiftController.
 */

namespace Drupal\lift\Controller;

use Drupal\Core\Asset\AssetCollectionRendererInterface;
use Drupal\Core\Asset\AssetResolverInterface;
use Drupal\Core\Asset\AttachedAssets;
use Drupal\Core\Entity\Controller\EntityViewController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for Lift routes.
 */
class LiftController extends EntityViewController {

  use StringTranslationTrait;

  /**
   * The asset resolver.
   *
   * @var \Drupal\Core\Asset\AssetResolverInterface
   */
  protected $assetResolver;

  /**
   * The CSS collection renderer.
   *
   * @var \Drupal\Core\Asset\AssetCollectionRendererInterface
   */
  protected $cssCollectionRenderer;

  /**
   * The JS collection renderer.
   *
   * @var \Drupal\Core\Asset\AssetCollectionRendererInterface
   */
  protected $jsCollectionRenderer;

  /**
   * Creates an EntityViewController object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Asset\AssetResolverInterface $asset_resolver
   *   The asset resolver.
   * @param \Drupal\Core\Asset\AssetCollectionRendererInterface $css_collection_renderer
   *   The CSS collection renderer.
   * @param \Drupal\Core\Asset\AssetCollectionRendererInterface $js_collection_renderer
   *   The JS collection renderer.
   */
  public function __construct(EntityManagerInterface $entity_manager, RendererInterface $renderer, AssetResolverInterface $asset_resolver, AssetCollectionRendererInterface $css_collection_renderer, AssetCollectionRendererInterface $js_collection_renderer) {
    $this->entityManager = $entity_manager;
    $this->renderer = $renderer;
    $this->assetResolver = $asset_resolver;
    $this->cssCollectionRenderer = $css_collection_renderer;
    $this->jsCollectionRenderer = $js_collection_renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('renderer'),
      $container->get('asset.resolver'),
      $container->get('asset.css.collection_renderer'),
      $container->get('asset.js.collection_renderer')
    );
  }

  /**
   * Returns a JSON representing the rendered entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being rendered.
   * @param string $view_mode
   *   The view mode that should be used to display the entity.
   * @param string $langcode
   *   For which language the entity should be rendered.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON representation of the rendered entity, with the following keys:
   *   - body: The markup of the entity itself.
   *   - styles: The markup for any CSS associated with this entity.
   *   - scripts: The markup for any JS belonging in the <head> element that is
   *     associated with this entity.
   *   - scripts_bottom: The markup for any JS belonging in the bottom of the
   *     <body> element that is associated with this entity.
   *   - head: The markup for any other elements to be in <head>.
   */
  public function getRenderedEntity(EntityInterface $entity, $view_mode, $langcode) {
    $render_array = $this->view($entity, $view_mode, $langcode);

    $result['body'] = $this->renderer->render($render_array);
    $all_assets = $this->gatherAssetMarkup($render_array);
    $result += $this->renderAssets($all_assets);

    return new JsonResponse($result);
  }

  /**
   * Renders an array of assets.
   *
   * @param array $all_assets
   *   An array of all unrendered assets keyed by type.
   *
   * @return array
   *   An array of all rendered assets keyed by type.
   */
  protected function renderAssets(array $all_assets) {
    $result = [];
    foreach ($all_assets as $asset_type => $assets) {
      $result[$asset_type] = [];
      foreach ($assets as $asset) {
        $result[$asset_type][] = $this->renderer->render($asset);
      }
    }
    return $result;
  }

  /**
   * Gathers the markup for each type of asset.
   *
   * @param array $render_array
   *   The render array for the entity.
   *
   * @return array
   *   An array of rendered assets. See self::getRenderedEntity() for the keys.
   *
   * @todo template_preprocess_html() should be split up and made reusable.
   */
  protected function gatherAssetMarkup(array $render_array) {
    $assets = AttachedAssets::createFromRenderArray($render_array);

    // Render the asset collections.
    $css_assets = $this->assetResolver->getCssAssets($assets, FALSE);
    $variables['styles'] = $this->cssCollectionRenderer->render($css_assets);

    list($js_assets_header, $js_assets_footer) = $this->assetResolver->getJsAssets($assets, FALSE);
    $variables['scripts'] = $this->jsCollectionRenderer->render($js_assets_header);
    $variables['scripts_bottom'] = $this->jsCollectionRenderer->render($js_assets_footer);

    // Handle all non-asset attachments.
    drupal_process_attached($render_array);
    $variables['head'] = drupal_get_html_head(FALSE);

    return $variables;
  }

  /**
   * Returns a JSON representation of the entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity being viewed.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON representation of the entity.
   */
  public function getEntityData(EntityInterface $entity) {
    return new JsonResponse($entity->toArray());
  }

  /**
   * Route title callback.
   *
   * @param \Drupal\Core\Entity\EntityInterface $lift_block
   *   The Lift block entity.
   *
   * @return string
   *   The title for the Lift block edit form.
   */
  public function editLiftBlockTitle(EntityInterface $lift_block) {
    return $this->t('Edit %label Lift block', ['%label' => $lift_block->label()]);
  }

}
