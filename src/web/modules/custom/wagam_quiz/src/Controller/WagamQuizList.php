<?php

namespace Drupal\wagam_quiz\Controller;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Controller\ControllerBase;
use Drupal\views\Views;

class WagamQuizList extends ControllerBase{

    public function quizListView(){
        $build = [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    'quiz-list',
                ],
            ],
        ];
        $entityTypeManager = \Drupal::entityTypeManager();
        $taxonomy_storage = $entityTypeManager->getStorage('taxonomy_term');
        $classList = $taxonomy_storage->loadByProperties(['vid' => 'niveau_scolaire']);
        $brancheList = $taxonomy_storage->loadByProperties(['vid' => 'branche_mathematiques']);


        /** @var \Drupal\taxonomy\Entity\Term $classe */

        foreach($classList as $key => $classe){
            $classe_total_count = 0;
            $build[$key]['title'] = [
                '#type' => 'html_tag',
                '#tag' => 'h2',
                '#attributes' => [
                    'class' => [
                        'class-title'
                    ],
                ],
                'content' => [
                    '#markup' => new FormattableMarkup('Exercice classe de @classe' , ['@classe'  => $classe->getName()]),
                ],
            ];
            /** @var \Drupal\taxonomy\Entity\Term $branche */
            foreach($brancheList as $index => $branche){
                $view = Views::getView('sexercer');
                $view->setDisplay('block_1');
                $view->setArguments([$branche->id(), $classe->id()]);
                $view->execute();
                $result = $view->render();

                if($view->total_rows > 0){
                    $color = $branche->get('field_code_couleur')->first()->getString();
                    $build[$key]['title']['#attributes']['style'] = '--branche-color: '.$color.';';
                    $build[$key][$index]['branche_title'] = [
                        '#type' => 'container',
                        '#attributes' => ['class' => 'branche-title-wrapper'],
                        'content' => [
                            '#type' => 'html_tag',
                            '#tag' => 'h3',
                            '#attributes' => [
                                'class' => [
                                    'branche-title',
                                ],
                                'style' => '--branche-color: '.$color.';',
                            ],
                            'content' => [
                                '#markup' => $branche->getName(),
                            ],
                        ],
                    ];
                    $build[$key][$index]['items'][] = ['#markup'=> \Drupal::service('renderer')->render($result)];
                    $classe_total_count = $classe_total_count + $view->total_rows;
                }
            }
            if($classe_total_count == 0){
                unset($build[$key]);
            }
        }
        return $build;
    }

}