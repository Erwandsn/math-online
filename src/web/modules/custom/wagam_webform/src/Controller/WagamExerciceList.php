<?php

namespace Drupal\wagam_webform\Controller;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Block\BlockManagerInterface;
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

  protected $blockManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager, RendererInterface $renderer, BlockManagerInterface $blockManager)
  {
    $this->entityTypeManager = $entityTypeManager;
    $this->renderManager = $renderer;
    $this->blockManager = $blockManager;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('plugin.manager.block')
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
      $block = $this->blockManager->createInstance('wagam_form_detail', ['webform' => $form, 'type' => 'teaser'])-> build();
      unset($block['related_lesson']);
      $block['#type'] = 'container';
      $block['#attributes'] = [
        'class' => [
          'webform-detail-wrapper',
          'col-12',
          'col-md-4',
          'align-self-strech',
        ],
      ];
      $build['content'][$id] = $block;
    }

    return $build;
  }
}
