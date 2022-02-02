<?php

namespace Drupal\wagam_dashboard\Controller;

use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DashBoardController extends ControllerBase{

    protected $currentUser;

    protected $entityTypeManager;

    protected $formBuilder;

    protected $plugingBlockManager;

    public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entityTypeManager, FormBuilderInterface $formBuilder, BlockManagerInterface $blockManager)
    {
        $this->currentUser = $currentUser;
        $this->entityTypeManager = $entityTypeManager;
        $this->formBuilder = $formBuilder;
        $this->plugingBlockManager = $blockManager;
    }

    public static function create(ContainerInterface $container){
        return new static(
            $container->get('current_user'),
            $container->get('entity_type.manager'),
            $container->get('form_builder'),
            $container->get('plugin.manager.block')
        );
    }

    /**
     * Méthode de la page /mon-mot-de-passe
     */
    public function passwordEditView(){
        //Init container global
        $build = $this->preparePage();

        //Preparing form
        $current_user = $this->entityTypeManager->getStorage('user')->load($this->currentUser()->id());
        $formObject = \Drupal::entityTypeManager()->getFormObject('user', 'default')->setEntity($current_user);
        $user_profile_form = $this->formBuilder->getForm($formObject);

        $user_profile_form['field_date_de_naissance']['#access'] = FALSE;
        $user_profile_form['field_nom']['#access'] = FALSE;
        $user_profile_form['field_prenom']['#access'] = FALSE;
        $user_profile_form['field_superviseurs']['#access'] = FALSE;
        $user_profile_form['user_picture']['#access'] = FALSE;
        $user_profile_form['timezone']['#access'] = FALSE;
        $user_profile_form['language']['#access'] = FALSE;
        $user_profile_form['account']['roles']['#access'] = FALSE;
        $user_profile_form['account']['status']['#access'] = FALSE;
        unset($user_profile_form['group_mes_superviseurs']);
        $user_profile_form['account']['mail']['#attributes']['disabled'] = 'true';
        $user_profile_form['account']['name']['#attributes']['disabled'] = 'true';
        $user_profile_form['account']['current_pass']['#weight'] = 10;
        $user_profile_form['#attributes']['class'][] = 'col-md-9';

        $build['container']['user_form'] = $user_profile_form;

        return $build;
    }

    /**
     * Action effectuée sur la page /mon-profil
     */
    public function dashboardView(){

        //Init container global
        $build = $this->preparePage();

        //Preparing form
        $current_user = $this->entityTypeManager->getStorage('user')->load($this->currentUser()->id());
        $formObject = \Drupal::entityTypeManager()->getFormObject('user', 'default')->setEntity($current_user);
        $user_profile_form = $this->formBuilder->getForm($formObject);
        $user_profile_form['account']['#access'] = FALSE;
        $user_profile_form['timezone']['#access'] = FALSE;
        $user_profile_form['language']['#access'] = FALSE;
        $user_profile_form['#attributes']['class'][] = 'col-md-9';

        $build['container']['user_form'] = $user_profile_form;
        //Superviseur
        return $build;
    }

    /**
     * Prepare la base commune des page
     */
    private function preparePage(){
          //Init container global
          $build['container'] = [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    'row',
                ],
            ],
        ];

        //Preparing sidebar
        $block_instance = $this->plugingBlockManager->createInstance('dashboard_sidebar', []);
        $build['container']['sidebar'] = $block_instance->build();
        return $build;
    }

}