<?php

namespace Drupal\wagam_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\MenuLinkTree;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with Dashboad - Sidebard Menu.
 *
 * @Block(
 *   id = "wagam_chapter_tree",
 *   admin_label = @Translation("WaGamMaths - Bloc des lecons dans le chapitre en cours."),
 *   category = @Translation("WaGamMaths"),
 * )
 */
class ChapterTreeBlock extends BlockBase implements ContainerFactoryPluginInterface
{
  /**
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static (
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * @inheritDoc
   */
  public function getCacheContexts()
  {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

  public function build()
  {
    $build = [
      'wrapper' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'col-12',
            'col-md-4',
            'col-lg-3',
            'd-none',
            'd-md-block',
            'chapter-content-wrapper',
          ],
          'data-nid' => NULL,
        ],
      ],
      'mobile_toggle_wrapper' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'col-12',
            'mobile-toggler-wrapper',
            'd-md-none',
          ],
        ],
        'toggle_action' => [
          '#type' => 'html_tag',
          '#tag' => 'button',
          '#attributes' => [
            'class' => [
              'btn',
              'btn-secondary',
              'chapter-toggler'
            ],
            'aria-expanded' => 'false'
          ],
          'content' => [
            '#markup' => 'Voir le sommaire',
          ],
        ],
      ],
      '#attributes' => [
        'class' => [
          'chapter-block',
        ],
      ],
    ];

    if(!empty($this->configuration['node']) && $this->configuration['node'] instanceof NodeInterface && $this->configuration['node']->bundle() == 'lecon'){
      $current_lecon = $this->configuration['node'];
      $chapter = $this->loadChapterList($current_lecon);
      if(!empty($chapter)){
        $build['wrapper']['#attributes']['data-nid'] = $chapter;
        $build['wrapper']['chapter'] = $this->buildChapter($chapter);
      }
    }else if(!empty($this->configuration['node']) && $this->configuration['node'] instanceof NodeInterface && $this->configuration['node']->bundle() == 'chapitre'){
      $build['wrapper']['#attributes']['data-nid'] = $this->configuration['node'];
      $build['wrapper']['chapter'] = $this->buildChapter($this->configuration['node']);
    }

    return $build;
  }

  /**
   * Loading brothers chapter of the one in parameter.
   */
  public function loadChapterList(NodeInterface $current){
    $node_storage = $this->entityTypeManager->getStorage('node');
    $query = $node_storage->getQuery();
    $query->condition('type', 'chapitre')
      ->condition('field_lecons_du_chapitre', $current->id(), 'IN');
    $result = $query->execute();
    if(!empty($result)){
      $result = $node_storage->load($result[array_key_first($result)]);
    }
    return $result;
  }

  private function buildChapter(NodeInterface $chapter){
    $view_builder = $this->entityTypeManager->getViewBuilder('node');
    return $view_builder->view($chapter, 'tree_link');
  }
}
