<?php

namespace Drupal\wagam_quiz\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class WagamQuizListFilter extends FormBase{

    const FORMID = "wagam.exercices.filter.form";

    public function getFormId()
    {
        return self::FORMID;
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form = [
            '#type' => 'container',
            '#attributes' => [
                'class' => [
                    ''
                ],
            ],
            '#filter_branche' => [
                '#type' => 'select',
                '#empty' => 'Filter sur un theme mathémathiques'
            ],
        ];
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {

    }

}