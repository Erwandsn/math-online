<?php

namespace Drupal\wagam_home\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class WagamHomeConfigurationForm extends ConfigFormBase
{
  const FORM_ID = 'wagam.home.form';

  const CONFIG = 'wagam_home.home.settings';

  protected function getEditableConfigNames()
  {
    return [
      self::CONFIG,
    ];
  }

  public function getFormId()
  {
    return self::FORM_ID;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->configFactory->get(self::CONFIG);

    $bloc_principal_config = $config->get('bloc_principal');

    $form['bloc_principal'] = [
      '#type' => 'fieldset',
      '#title' => 'Configuration du bloc principal',
      '#tree' => true,
    ];

    $form['bloc_principal']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Titre du bloc'),
      '#default_value' => !empty($bloc_principal_config) ? $bloc_principal_config['title'] : '',
    ];

    $form['bloc_principal']['second_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Texte secondaire'),
      '#default_value' => !empty($bloc_principal_config) ? $bloc_principal_config['second_title'] : '',
    ];

    $form['bloc_principal']['btn'] = [
      '#type' => 'container',
      '#prefix' => "<h3>Bouton de redirection</h3>",
      '#attributes' => [
        'style' => [
          'border: 1px solid #c0c0c0;',
          'padding: 0.5rem 1rem;',
        ]
      ]
    ];

    $form['bloc_principal']['btn']['btn_label'] = [
      '#type' => 'textfield',
      '#title' => 'Texte du bouton',
      '#default_value' => !empty($bloc_principal_config) ? $bloc_principal_config['btn']['btn_label'] : '',
    ];

    $form['bloc_principal']['btn']['btn_url'] = [
      '#type' => 'url',
      '#title' => 'Url de redirection',
      '#default_value' => !empty($bloc_principal_config) ? $bloc_principal_config['btn']['btn_url'] : '',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   *
   * @return void
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $config = $this->configFactory->getEditable(self::CONFIG);
    $keys = ['bloc_principal'];
    foreach ($keys as $entry){
      $config->set($entry, $form_state->getValue($entry));
    }
    try{
      $config->save();
      \Drupal::messenger()->addStatus("Configuration de la page d'accueil sauvegardée.", FALSE);
    }catch (\Exception $e){
      \Drupal::messenger()->addError("Impossible de sauvegarder la configuration veuillez contacter un administrateur.", FALSE);
    }
  }
}
