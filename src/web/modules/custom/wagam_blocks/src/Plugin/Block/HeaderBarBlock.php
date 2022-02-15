<?php

namespace Drupal\wagam_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\MenuLinkTree;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\wagam_blocks\Form\GeneralForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with Dashboad - Sidebard Menu.
 *
 * @Block(
 *   id = "wagam_header_block",
 *   admin_label = @Translation("WaGamMaths - Block d'entête du site"),
 *   category = @Translation("WaGamMaths"),
 * )
 */
class HeaderBarBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    /**
     * @var ConfigFactory
     */
    protected $configManager;

     /**
     * @var MenuLinkTree
     */
    protected $menuTreeService;

    /**
     * @var EntityTypeManager
     */
    protected $entityManager;

    public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $configFactory, MenuLinkTree $menuTreeStorageInterface, EntityTypeManagerInterface $entityTypeManager)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->configManager = $configFactory;
        $this->menuTreeService = $menuTreeStorageInterface;
        $this->entityManager = $entityTypeManager;
    }

    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static (
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('config.factory'),
            $container->get('menu.link_tree'),
            $container->get('entity_type.manager')
        );
    }

    /**
    * @inheritDoc
    */
    public function defaultConfiguration()
    {
        return [];
    }

    /**
    * @inheritDoc
    */
    public function getCacheContexts()
    {
      return Cache::mergeContexts(parent::getCacheContexts(), ['user.roles']);
    }

  /**
   * @inheritDoc
   */
    public function build()
    {
        //Get configuration
        $config = $this->configManager->get(GeneralForm::CONFIG);
        $header_config = $config->get('header');

        //Init bloc content
        $build['content'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    'wagam-header',
                    'row',
                    'justify-content-between'
                ],
            ],
        ];

        $build['content']['logo_wrapper'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    'branding-group',
                    'col-9',
                ],
            ],
        ];

        //Adding logo
        if(!empty($header_config) && isset($header_config['logo'])){
          $media_logo = $this->entityManager->getStorage('media')->load($header_config['logo']);
          $imageStyle = $this->entityManager->getStorage('image_style')->load('thumbnail');
          if(!empty($media_logo)){
            $logo_path = $media_logo->get('field_media_image')->entity->uri->getString();
            $build['content']['logo_wrapper']['logo'] = [
              '#type' => 'html_tag',
              '#tag' => 'img',
              '#attributes' =>  [
                'src' => $imageStyle->buildUrl($logo_path) ,
                'class' => [
                  'branding-logo',
                ],
                'alt' => $this->t('Logo WaGamMaths'),
              ],
            ];
          }
        }

        $build['content']['logo_wrapper']['site_name'] = [
          '#type' => 'html_tag',
          '#tag' => 'h1',
          '#value' => 'WaGamMaths',
          '#attributes' =>  [
            'class' => [
              'site-name',
            ],
          ],
        ];

        //Preparing Menu wrapper
        $build['content']['menu_wrapper'] = $this->prepareMainMenuWrapper()['menu_wrapper'];

        //Adding menu to bloc content
        $build['content']['menu_wrapper']['offcanvas_root']['body'][] = $this->loadUserMenu();
        $build['content']['menu_wrapper']['offcanvas_root']['body'][] = $this->loadMainMenu();

        //Managing cahce to rebuild block when config is changed.
        $cache = new CacheableMetadata();
        $cache->createFromObject($config);
        $cache->applyTo($build);

        return $build;
    }

    private function loadUserMenu(){
        $menu_parameters = new \Drupal\Core\Menu\MenuTreeParameters();
        $menu_parameters->setMaxDepth(2); // Profondeur du menu à afficher
        $menu_name = 'account'; // Nom machine du menu à afficher
        $menuLoaded = $this->menuTreeService->load($menu_name, $menu_parameters);

        //Manipulators
        $manipulators = array(
            // Only show links that are accessible for the current user.
            array('callable' => 'menu.default_tree_manipulators:checkAccess'),
            // Use the default sorting of menu links.
            array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
        );
        $tree = $this->menuTreeService->transform($menuLoaded, $manipulators);

        return $this->menuTreeService->build($tree);
    }

    private function loadMainMenu(){
        //Loading menu
        $menu_parameters = new \Drupal\Core\Menu\MenuTreeParameters();
        $menu_parameters->setMaxDepth(3); // Profondeur du menu à afficher
        $menu_name = 'main'; // Nom machine du menu à afficher
        $menuLoaded = $this->menuTreeService->load($menu_name, $menu_parameters);

        //Manipulators
        $manipulators = array(
            // Only show links that are accessible for the current user.
            array('callable' => 'menu.default_tree_manipulators:checkAccess'),
            // Use the default sorting of menu links.
            array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
        );
        $tree = $this->menuTreeService->transform($menuLoaded, $manipulators);

        return $this->menuTreeService->build($tree);
    }

    private function prepareMainMenuWrapper(){
        $build['menu_wrapper'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    'col-3',
                    'd-flex',
                    'justify-content-end'
                ],
            ],
        ];

        $build['menu_wrapper']['toggler'] = [
            '#type' => 'html_tag',
            '#tag' => 'button',
            '#attributes' => [
                'type' => 'button',
                'class' => [
                    'navbar-toggler'
                ],
                'data-bs-toggle' => 'offcanvas',
                'data-bs-target' => '#menu--main',
                'aria-expanded' => 'false',
                'aria-controls' => 'menu--main',
            ],
        ];

        $build['menu_wrapper']['toggler']['icon'] = [
            '#markup' => '<span class="navbar-toggler-icon"></span>'
        ];

        $build['menu_wrapper']['offcanvas_root'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    'offcanvas',
                    'offcanvas-end'
                ],
                'tabindex' => '-1',
                'id' => 'menu--main',
                'aria-label' => $this->t('Menu Principal'),
            ],
        ];

        $build['menu_wrapper']['offcanvas_root']['header'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    'offcanvas-header'
                ],
            ],
        ];

        $build['menu_wrapper']['offcanvas_root']['header']['close_canvas'] = [
            '#type' => 'html_tag',
            '#tag' => 'button',
            '#attributes' => [
                'type' => 'button',
                'class' => [
                    'btn-close',
                    'text-reset',
                ],
                'data-bs-dismiss' => 'offcanvas',
                'aria-label' => $this->t('Fermer'),
            ],
        ];

        $build['menu_wrapper']['offcanvas_root']['body'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    'offcanvas-body'
                ],
            ],
        ];

        return $build;
    }
}
