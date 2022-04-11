<?php

namespace Drupal\wagam_home\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the homepage main block.
 *
 * @Block(
 *   id = "home_features",
 *   admin_label = @Translation("Bloc principal de la page d'accueil"),
 * )
 */
class HomeFeaturesBlock extends BlockBase{

    protected $entityTypeManager;

    public function build(){
         return [
             '#markup' => 'Wagam Features'
        ];
    }

     /**
     * Overrides \Drupal\block\BlockBase::blockForm().
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $form['wrapper'] = [
            '#type' => 'container',
            '#attributes' => [
                'id' => 'feature-list',
            ],
        ];


        $form['actions'] = [
            '#type' => 'actions',
            [
                '#type' => 'button',
                '#value' => 'Ajouter un bloc fonctionalité',
            ],
        ];

        return $form;
    }

    /**
     * Overrides \Drupal\block\BlockBase::blockSubmit().
    */
    public function blockSubmit($form, FormStateInterface $form_state) {
        // $this->configuration['welcome_text'] = $form_state->getValue('welcome_text');
    }
}