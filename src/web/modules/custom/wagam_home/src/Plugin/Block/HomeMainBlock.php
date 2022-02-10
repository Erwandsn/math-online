<?php

namespace Drupal\wagam_home\Plugin\Block;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the homepage main block.
 *
 * @Block(
 *   id = "home_main",
 *   admin_label = @Translation("Bloc principal de la page d'accueil"),
 * )
 */
class HomeMainBlock extends BlockBase implements ContainerFactoryPluginInterface
{

  protected $configFactory;

  /**
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param ConfigFactoryInterface $configFactory
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $configFactory)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $configFactory;
  }

  /**
   * @param ContainerInterface $container
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @return HomeMainBlock|static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
    );
  }

  /**
   * @inheritDoc
   */
  public function build()
  {
    $config = $this->configFactory->get('wagam.home.settings');
    $main_config = $config->get('bloc_principal');

    $build = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
            'home-main-bloc',
        ],
      ],
    ];

    $build['content'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'bloc--content',
        ],
      ],
    ];

    if(!empty($main_config)){
      if(!empty($main_config['title'])){
        $build['content']['title'] = [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#attributes' => [
            'class' => [
              'main-bloc--title',
            ],
          ],
          'label' => [
            '#markup' => $main_config['title'],
          ],
        ];
      }


      if(!empty($main_config['title'])){
        $build['content']['sub_text'] = [
          '#type' => 'processed_text',
          '#text' => '<p class="secondary-texte">'.$main_config['second_title'].'</p>',
          '#format' => filter_default_format(),
        ];
      }

      if(!empty($main_config['btn'])){
        $label = new FormattableMarkup('<i class="fas fa-arrow-right"></i> @text', ['@text' =>  $main_config['btn']['btn_label']]);
        $build['content']['btn'] = [
          '#type' => 'html_tag',
          '#tag' => 'a',
          '#attributes' => [
            'class' => [
              'btn',
              'btn-green',
            ],
            'href' => $main_config['btn']['btn_url'],
          ],
          'label' => [
            '#markup' => $label->__toString(),
          ],
        ];
      }
    }

    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($config);
    $cache->applyTo($build);

    return $build;
  }
}
