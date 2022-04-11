<?php

namespace Drupal\wagam_home\Controller;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HomeController extends ControllerBase
{

  protected $currentUser;

  protected $entityTypeManager;

  protected $formBuilder;

  protected $plugingBlockManager;

  protected $renderer;

  public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entityTypeManager, FormBuilderInterface $formBuilder, BlockManagerInterface $blockManager, RendererInterface $rendererInterface)
  {
    $this->currentUser = $currentUser;
    $this->entityTypeManager = $entityTypeManager;
    $this->formBuilder = $formBuilder;
    $this->plugingBlockManager = $blockManager;
    $this->renderer = $rendererInterface;
  }

  public static function create(ContainerInterface $container){
    return new static(
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('form_builder'),
      $container->get('plugin.manager.block'),
      $container->get('renderer')
    );
  }

  /**
   * Building Home.
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function homeView(){
    $build = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'wagam-home-wrapper'
        ],
      ],
    ];

    //Loading and building home main bloc
    $main_block = $this->plugingBlockManager->createInstance('home_main', []);
    $build['home_main_bloc'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'main-wrapper'
        ]
      ],
      'content' => $main_block->build(),
    ];


    //Loading and building home account bloc
    $account_block = $this->plugingBlockManager->createInstance('wagam_account_block', []);
    $built_account_block = $account_block->build();

    $build['account_home_block'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'd-flex',
          'flex-column',
        ],
      ],
      'content' =>  [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'col-12',
            'col-md-8',
            'col-lg-6',
            'offset-md-2',
            'offset-lg-3',
            'home-user-profile-wrapper',
          ],
        ],
        'card' => $built_account_block['card'],
      ]
    ];

    $this->renderer->addCacheableDependency($build, $account_block);

    return $build;
  }
}
