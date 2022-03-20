<?php

namespace Drupal\wagam_webform\Controller;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\webform\WebformInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller overview d'un formulaire
 */
class WagamFormOverview extends ControllerBase
{

  protected $entityTypeManager;

  protected $blockManager;

  /**
   * @inheritDoc
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, BlockManagerInterface $blockManager)
  {
    $this->entityTypeManager = $entityTypeManager;
    $this->blockManager = $blockManager;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.block')
    );
  }

  /**
   * Overview action.
   * @param Request $request
   * @param WebformInterface $webform
   *
   * @return array[]
   */
  public function overviewView(Request $request, WebformInterface $webform)
  {
    $form_detail_instance = $this->blockManager->createInstance('wagam_form_detail', ['webform' => $webform]);
    $submission_detail = $this->blockManager->createInstance('wagam_form_submissions', ['webform' => $webform]);
    $build = [
      'content' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'webform-overview-wrapper',
            'row',
          ]
        ],
        'form_detail' => [
          '#type' => 'container',
          '#attributes' =>  [
            'class' => [
              'webform-detail-wrapper',
              'col-12',
              'col-md-6',
            ],
          ],
          'content' => $form_detail_instance->build(),
        ],
        'form_submissions' => [
          '#type' => 'container',
          '#attributes' =>  [
            'class' => [
              'webfrom-submission-wrapper',
              'col-12',
              'col-md-6',
            ]
          ],
          'content' => [
            'title' => [
              '#markup' => '<h3 class="text-align-center">Mes résultats</h3>',
            ],
            'submissions_block' =>  $submission_detail->build(),
          ]
        ],
      ],
    ];

    return $build;
  }
}
