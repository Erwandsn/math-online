<?php

namespace Drupal\wagam_webform\Controller;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\Element\Form;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;
use Drupal\webform\WebformEntityStorage;
use Drupal\webform\WebformInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WagamExerciceList extends ControllerBase
{
  protected $renderManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager, RendererInterface $renderer)
  {
    $this->entityTypeManager = $entityTypeManager;
    $this->renderManager = $renderer;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('renderer')
    );
  }

  public function exerciceListView(){

    /** @var WebformEntityStorage $webform_storage */
    $webform_storage = $this->entityTypeManager->getStorage('webform');
    $form_list = $webform_storage->loadByProperties();

    if(!empty($form_list)){
      $build = $this->buildList($form_list)['content'];
    }else{
      $build['no_result_wrapper'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'alert',
            'alert-primary',
            'd-flex',
            'align-items-center',
          ],
          'role' => 'alert',
        ],
      ];

      $msg_markup = new FormattableMarkup("<div><i class='fas fa-exclamation-triangle'></i>@message</div>", ['@message' => 'Aucun exercice disponible']);
      $build['no_result_wrapper']['message'] = $msg_markup->__toString();
    }

    return $build;
  }

  /**
   * @param array $form_list
   * @return array
   */
  private function buildList(array $form_list){

    $build['content'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'card-list',
          'exercice-list-wrapper',
          'd-flex',
          'flex-column',
          'flex-md-row'
        ],
      ],
    ];

    foreach($form_list as $id => $form){
      $build['content'][$id] = $this->buildCard($form)['card'];
    }

    return $build;
  }

  private function buildCard(WebformInterface $form){
    $build['card'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'card',
          'col-12',
          'col-md-4',
          'align-self-strech',
        ],
      ],
    ];

    $build['card']['card_header'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'card-header',
        ],
      ],
    ];

    $build['card']['card_header']['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => $form->get('title'),
      '#attributes' => [
        'class' => [
          'card-title',
        ]
      ]
    ];


    $build['card']['card_body'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'card-body',
          'd-flex',
          'flex-column',
          'justify-content-between'
        ],
      ],
    ];

    $build['card']['card_body']['text_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'card-text',
          'row',
          'd-flex',
          'flex-row',
          'align-items-center',
        ]
      ]
    ];

    $nb_question_markup = new FormattableMarkup('<p class="nb-question"><i class="fas fa-question"></i> @nb @label</p>', ['@nb' => '0', '@label' => $this->t('Quesions')]);
    $build['card']['card_body']['text_wrapper']['nb_question']['#markup'] = $nb_question_markup->__toString();

    $build['card']['card_body']['launch'] = [
      '#type' => 'html_tag',
      '#tag' => 'a',
      '#attributes' => [
        'class' =>[
          'btn',
          'btn-secondary',
          'col-12'
        ],
        'href' => $form->toLink()->getUrl()->toString(),
      ],
    ];

    $build['card']['card_body']['launch']['icon'] = [
      '#markup' => '<i class="fas fa-rocket"></i>'
    ];

    $build['card']['card_body']['launch']['text'] = [
      '#markup' => $this
        ->t('Lancer l\'éxercice'),
    ];

    return $build;
  }
}
