<?php

namespace Drupal\wagam_dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Menu\MenuLinkTree;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with Dashboad - Sidebard Menu.
 *
 * @Block(
 *   id = "dashboard_sidebar",
 *   admin_label = @Translation("Sidebard du dashboard"),
 * )
 */
class DashBoardSideBar extends BlockBase implements ContainerFactoryPluginInterface{

    /**
     * @var MenuLinkTree
     */
    protected $menuTreeService;


    public function __construct(MenuLinkTree $menuTreeStorageInterface)
    {
        $this->menuTreeService = $menuTreeStorageInterface;
    }

    /**
     * @inheritDoc
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $container->get('menu.link_tree')
        );
    }

  /**
   * @inheritDoc
   */
    public function getCacheContexts()
    {
      return Cache::mergeContexts(parent::getCacheContexts(), ['user']);
    }

  /**
     * @inheritDoc
     */
    public function build(){

        //Loading menu
        $menu_parameters = new \Drupal\Core\Menu\MenuTreeParameters();
        $menu_parameters->setMaxDepth(1); // Profondeur du menu à afficher
        $menu_name = 'dashboard---sidebard'; // Nom machine du menu à afficher
        $menuLoaded = $this->menuTreeService->load($menu_name, $menu_parameters);

        //Manipulators
        $manipulators = array(
            // Only show links that are accessible for the current user.
            array('callable' => 'menu.default_tree_manipulators:checkAccess'),
            // Use the default sorting of menu links.
            array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
        );
        $tree = $this->menuTreeService->transform($menuLoaded, $manipulators);

        $render = $this->menuTreeService->build($tree);
        //Menu loaded and rendered.

        //Customing css class
        $render['#attributes']['class'] = [
            'col-md-3',
            'flex-column',
            'dashboard-sidebard'
        ];

        // dd($render);
        return $render;
    }
}
