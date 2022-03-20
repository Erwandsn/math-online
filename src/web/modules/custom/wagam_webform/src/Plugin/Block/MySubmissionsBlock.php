<?php

namespace Drupal\wagam_webform\Plugin\Block;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block with Dashboad - Sidebard Menu.
 *
 * @Block(
 *   id = "wagam_form_submissions",
 *   admin_label = @Translation("WaGamMaths - Bloc Listant les réponses et resultats a un questionnaire"),
 *   category = @Translation("WaGamMaths"),
 * )
 */
class MySubmissionsBlock extends BlockBase implements ContainerFactoryPluginInterface
{
  protected EntityTypeManagerInterface $entityTypeManager;

  protected $currentUser;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, AccountProxyInterface $accountProxy)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $accountProxy;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static (
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * @inheritDoc
   */
  public function getCacheContexts()
  {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url', 'user']);
  }

  /**
   * @return array|void
   */
  public function build()
  {
    $build = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'my-submission-list',
          'row'
        ]
      ],
    ];

    $submission_storage = $this->entityTypeManager->getStorage('webform_submission');

    $submissionList = $submission_storage->loadByProperties([
      'webform_id' => $this->configuration['webform']->id(),
      'uid' => $this->currentUser->id(),
    ]);

    $submission_ids_list = $submission_storage->getQuery()
      ->condition('webform_id', $this->configuration['webform']->id())
      ->condition('uid', $this->currentUser->id())
      ->sort('created', 'desc')
      ->execute();

    if(!empty($submission_ids_list)){
      $submissionList = $submission_storage->loadMultiple($submission_ids_list);
      foreach ($submissionList as $item) {
        $submission_timestamp = $item->get('created')->first()->getString();
        $submission_date = new DrupalDateTime();
        $submission_date->setTimestamp($submission_timestamp);

        $submission_score = $item->get('webform_score')->first()->getValue();
        $score_markup = new FormattableMarkup('<span class="note">@score</span> / <span class="total">@total</span>', ['@score' => $submission_score['numerator'], '@total' => $submission_score['denominator']]);

        $build['items'][] = [
          '#type' => 'container',
          '#attributes' => [
            'class' => [
              'card',
              'submission-item-teaser',
              'col-12',
              'col-md-6'
            ]
          ],
          'wrapper' => [
            '#type' => 'container',
            '#attributes' => [
              'class' => [
                'row',
              ],
            ],
            'heading' => [
              '#type' => 'container',
              '#attributes' => [
                'class' => [
                  'col-4',
                  'score-wrapper'
                ],
              ],
              'score' => [
                '#type' => 'html_tag',
                '#tag' => 'p',
                '#attributes' => [
                  'class' => [
                    'score-area'
                  ],
                ],
                'content' => [
                  '#markup' => $score_markup->__toString(),
                ]
              ],
            ],
            'body' => [
              '#type' => 'container',
              '#attributes' => [
                'class' => [
                  'col-8',
                  'card-body',
                ]
              ],
              'date' => [
                '#type' => 'html_tag',
                '#tag' => 'p',
                'content' => [
                  '#markup' => '<small class="text-muted">'.$submission_date->format("j F Y \à H:i").'</small>',
                ]
              ],
              'correction_link' => [
                '#type' => 'link',
                '#title' => 'Voir la correction',
                '#url' => Url::fromRoute('entity.webform.user.submissions', ['webform' => $this->configuration['webform']->id(),'submission_view' => $item->id()]),
                '#attributes' => [
                  'class' => [
                    'btn',
                    'btn-secondary',
                  ]
                ]
              ],
            ],
          ],
        ];

      }
    }

    return $build;
  }
}
