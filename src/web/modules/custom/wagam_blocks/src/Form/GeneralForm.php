<?php

namespace Drupal\wagam_blocks\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Formulaire de configuration générale du site.
 */
class GeneralForm extends ConfigFormBase
{

    const CONFIG = 'wagam_blocks.general.config';
    const FORM_ID = 'wagam.general.config.form';

    public function getEditableConfigNames(){
        return [self::CONFIG];
    }

    public function getFormId()
    {
        return self::FORM_ID;
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->configFactory()->get(self::CONFIG);
        $header_config = $config->get('header');

        $form['header'] = [
            '#type' => 'fieldset',
            '#title' => 'Configuration de l\'entête du site.',
            "#tree" => TRUE,
        ];

        $form['header']['logo'] = [
            '#type' => 'media_library',
            '#allowed_bundles' => ['image'],
            '#title' => $this->t('Logo du site'),
            '#default_value' => isset($header_config['logo']) ? $header_config['logo'] : NULL,
            '#description' => $this->t('Sélectionner le logo du site'),
        ];

        $user_config = $config->get('user');

        $form['user'] = [
          '#type' => 'fieldset',
          '#title' => 'user',
          '#tree' => TRUE,
        ];

        $form['user']['default_user_picture'] = [
          '#type' => 'media_library',
          '#allowed_bundles' => ['image'],
          '#title' => $this->t('Image par défault utilisateur'),
          '#default_value' => isset($user_config['default_user_picture']) ? $user_config['default_user_picture'] : NULL,
          '#description' => $this->t('Sélectionner une image qui fera office d\'image par defaut si l\'utilisateur n\'en configure pas'),
        ];

        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->configFactory()->getEditable(self::CONFIG);
        $header_values = $form_state->getValue('header');
        $user_values = $form_state->getValue('user');
        if(!empty($header_values)){
            $config->set('header', $header_values);
        }

        if(!empty($user_values)){
          $config->set('user', $user_values);
        }
        $config->save();

        $this->messenger()->addStatus($this->t('Configuration enregistrée'));
    }
}
