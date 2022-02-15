<?php

namespace Drupal\wagam_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with Dashboad - Sidebard Menu.
 *
 * @Block(
 *   id = "wagam_lecon_auto_pager",
 *   admin_label = @Translation("WagamMaths - Block de pagination des menus."),
 *   category = @Translation("WaGamMaths"),
 * )
 */
class LeconPagerBlock extends BlockBase implements ContainerFactoryPluginInterface
{

  /**
   * @var EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityManager = $entityTypeManager;
  }

  /**
   * @param ContainerInterface $container
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @return LeconPagerBlock|static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * @inheritDoc
   */
  public function getCacheContexts()
  {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }


  /**
   * Building the block.
   * @return array
   */
  public function build()
  {
    $build = [
      'wrapper' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'lecon-pager-wrapper',
            'd-flex',
            'justify-content-between',
          ],
        ],
        'previous' => [
          '#title' => $this
            ->t('Leçon précédente'),
          '#type' => 'link',
          '#attributes' => [
            'class' => [
              'btn',
              'btn-secondary',
              'pager-previous',
            ],
          ],
          '#url' => '',
        ],
        'next' => [
          '#title' => $this
            ->t('Leçon suivante'),
          '#type' => 'link',
          '#attributes' => [
            'class' => [
              'btn',
              'btn-secondary',
              'pager-next',
            ],
          ],
          '#url' => '',
        ],
      ]
    ];


    if(isset($this->configuration['current_chapter']) && $this->configuration['current_chapter'] instanceof NodeInterface){
      $chapter = $this->configuration['current_chapter'];
      $current_lecon = $this->configuration['current_lecon'];
      $child_lecon = $chapter->get('field_lecons_du_chapitre')->referencedEntities();
      foreach ($child_lecon as $key => $lecon){
        if($lecon->id() === $current_lecon->id()){
          if(count($child_lecon) > 1 && $key === array_key_first($child_lecon)){
            $build['wrapper']['previous']['#access'] = false;
            if(isset($child_lecon[$key+1])){
              $build['wrapper']['next']['#title'] = 'Leçon suivante : '.$child_lecon[$key + 1]->getTitle();
              $build['wrapper']['next']['#url'] = Url::fromRoute('entity.node.canonical', ['node' => $child_lecon[$key+1]->id()]);
            }
          }elseif(count($child_lecon) > 1 && $key === array_key_last($child_lecon)){
            $build['wrapper']['next']['#access'] = false;
            if(isset($child_lecon[$key-1])) {
              $build['wrapper']['previous']['#title'] = 'Leçon précédente : '.$child_lecon[$key - 1]->getTitle();
              $build['wrapper']['previous']['#url'] = Url::fromRoute('entity.node.canonical', ['node' => $child_lecon[$key - 1]->id()]);
            }
          }else{
            if(count($child_lecon) <= 1){
              $build['wrapper']['previous']['#access'] = false;
              $build['wrapper']['next']['#access'] = false;
            }else{
              $build['wrapper']['previous']['#title'] =  'Leçon précédente : '.$child_lecon[$key - 1]->getTitle();
              $build['wrapper']['previous']['#url'] = Url::fromRoute('entity.node.canonical', ['node' => $child_lecon[$key - 1]->id()]);
              $build['wrapper']['next']['#title'] = 'Leçon suivante : '.$child_lecon[$key + 1]->getTitle();
              $build['wrapper']['next']['#url'] = Url::fromRoute('entity.node.canonical', ['node' => $child_lecon[$key + 1]->id()]);
            }
          }
        }
      }
    }

    return $build;
  }
}
