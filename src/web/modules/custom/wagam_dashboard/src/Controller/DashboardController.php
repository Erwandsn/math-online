<?php

namespace Drupal\wagam_dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DashBoardController extends ControllerBase{

    protected $currentUser;

    protected $entityTypeManager;

    protected $formBuilder;

    public function __construct(AccountProxyInterface $currentUser, EntityTypeManagerInterface $entityTypeManager, FormBuilderInterface $formBuilder)
    {
        $this->currentUser = $currentUser;
        $this->entityTypeManager = $entityTypeManager;
        $this->formBuilder = $formBuilder;
    }

    public static function create(ContainerInterface $container){
        return new static(
            $container->get('current_user'),
            $container->get('entity_type.manager'),
            $container->get('form_builder')
        );
    }

    public function dashboardView(){
        $current_user = $this->entityTypeManager->getStorage('user')->load($this->currentUser()->id());
        $formObject = \Drupal::entityTypeManager()->getFormObject('user', 'default')->setEntity($current_user);
        $user_profile_form = $this->formBuilder->getForm($formObject);
        $user_profile_form['account']['#access'] = FALSE;
        $user_profile_form['timezone']['#access'] = FALSE;
        $user_profile_form['language']['#access'] = FALSE;
        return $user_profile_form;
    }

}