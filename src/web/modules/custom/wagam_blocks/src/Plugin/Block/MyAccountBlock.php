<?php

namespace Drupal\wagam_blocks\Plugin\Block;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with Dashboad - Sidebard Menu.
 *
 * @Block(
 *   id = "wagam_account_block",
 *   admin_label = @Translation("WaGamMaths - Block de l'utilisateur courant"),
 *   category = @Translation("WaGamMaths"),
 * )
 */
class MyAccountBlock extends BlockBase implements ContainerFactoryPluginInterface
{

  protected $currentUser;

  protected $entityManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountProxyInterface $accountProxy, EntityTypeManagerInterface $entityTypeManager)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $accountProxy;
    $this->entityManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('entity_type.manager'),
    );
  }

  public function getCacheContexts()
  {
    return Cache::mergeContexts(parent::getCacheContexts(), ['user']);
  }

  public function build()
  {
    $user_storage = $this->entityManager->getStorage('user');

    $build['card'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'card',
          'wagam-current-user-block',
          'user-profil-card',
        ],
      ],
    ];

    $build['card']['card_body'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'card-body',
        ],
      ],
      '#weight' => 1,
    ];


    if($this->currentUser->getAccount() instanceof AnonymousUserSession){
      //Aucun utilisateur conecté
      $build['card']['card_body']['login_btn'] = [
        '#title' => $this
          ->t('Se connecter'),
        '#type' => 'link',
        '#attributes' => [
          'class' => [
            'btn',
            'btn-primary',
          ]
        ],
        '#url' => Url::fromRoute('user.login'),
      ];
    }else{
      //Un utilisateur est connecté
      $user_id = $this->currentUser->getAccount()->id();
      $user = $user_storage->load($user_id);
      $build['card']['card_header'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'card-header',
          ],
        ],
        '#weight' => 0,
      ];

      $imageStyle = $this->entityManager->getStorage('image_style')->load('image_de_profil');
      if(!$user->get('user_picture')->isEmpty()){
        $img_url = $user->get('user_picture')->first()->entity->uri->getString();
        $build['card']['card_header']['account_image'] = [
          '#type' => 'html_tag',
          '#tag' => 'img',
          '#attributes' => [
            'src' => $imageStyle->buildUrl($img_url),
            'class' => [
              'account-image',
            ],
          ],
        ];
      }

      $build['card']['card_header']['account_name']['#markup'] = '<p class="username">'.$this->currentUser->getDisplayName().'</p>';

      $build['card']['card_body']['exercice_link'] = [
        '#title' => $this
          ->t('S\'éxercer'),
        '#type' => 'link',
        '#attributes' => [
          'class' => [
            'btn',
            'btn-primary',
            'col-12',
          ],
        ],
        '#url' => '/exercices-quiz',
      ];

      $build['card']['card_body']['start_link'] = [
        '#title' => $this
          ->t('Commencer à apprendre'),
        '#type' => 'link',
        '#attributes' => [
          'class' => [
            'btn',
            'btn-primary',
            'col-12',
          ],
        ],
        '#url' => Url::fromRoute('view.chapitres.page_1'),
      ];

      $build['card']['card_body']['account_action_wrapper'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' =>  [
            'd-flex',
            'with-section',
            'justify-content-between'
          ]
        ],
      ];

      $build['card']['card_body']['account_action_wrapper']['my_account_link'] = [
        '#title' => $this
          ->t('Mon compte'),
        '#type' => 'link',
        '#attributes' => [
          'class' => [
            'btn',
            'btn-secondary',
            'col-6',
          ],
        ],
        '#url' => Url::fromRoute('wagam.dashboard'),
      ];

      $build['card']['card_body']['account_action_wrapper']['logout_link'] = [
        '#title' => $this
          ->t('Se déconnecter'),
        '#type' => 'link',
        '#attributes' => [
          'class' => [
            'btn',
            'btn-secondary',
            'col-6',
          ],
        ],
        '#url' => Url::fromRoute('user.logout'),
      ];
    }

    return $build;
  }
}
