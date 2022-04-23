<?php

namespace Drupal\wagam_blocks\Plugin\Block;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides a teaser block for exercice (webform).
 *
 * @Block(
 *   id = "wagam_exercice_teaser",
 *   admin_label = @Translation("WaGamMaths - Bloc affichant l'accroche d'un exercice"),
 *   category = @Translation("WaGamMaths"),
 * )
 */
class ExerciceTeaserBlock extends BlockBase{

    public function build(){
        $form = $this->getConfiguration()['webform'];

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
              'href' => Url::fromRoute('wagam.form_overview', ['webform' => $form->id()])->toString(),
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