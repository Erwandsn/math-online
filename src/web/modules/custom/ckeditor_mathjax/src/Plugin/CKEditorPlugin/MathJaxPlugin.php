<?php

namespace Drupal\ckeditor_mathjax\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "mathjax" plugin.
 *
 * @CKEditorPlugin(
 *   id = "mathjax",
 *   label = @Translation("Mathematical Formulas")
 * )
 */
class MathJaxPlugin extends CKEditorPluginBase implements CKEditorPluginConfigurableInterface {

  private $mathJaxLibrary = 'https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML';

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $settings = $editor->getSettings();

    return [
      'mathJaxClass' => !empty($settings['plugins']['mathjax']['mathjax_class']) ? Html::getClass($settings['plugins']['mathjax']['mathjax_class']) : 'my-math',
      'mathJaxLib' => !empty($settings['plugins']['mathjax']['mathjax_lib']) ? UrlHelper::stripDangerousProtocols($settings['plugins']['mathjax']['mathjax_lib']) : $this->mathJaxLibrary,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();

    $form['mathjax_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('MathJax CSS class'),
      '#description' => $this->t('Sets the default class for span elements that will be converted into Mathematical Formulas widgets.'),
      '#default_value' => !empty($settings['plugins']['mathjax']['mathjax_class']) ? $settings['plugins']['mathjax']['mathjax_class'] : 'my-math',
    );

    $form['mathjax_lib'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Path to the MathJax library'),
      '#description' => $this->t('Sets the path to the MathJax library. It can be both a local resource and a location different than the default CDN. Please note that this must be a full or absolute path.'),
      '#default_value' => !empty($settings['plugins']['mathjax']['mathjax_lib']) ? $settings['plugins']['mathjax']['mathjax_lib'] : $this->mathJaxLibrary,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return array(
      'Mathjax' => array(
        'label' => t('Math'),
        'image' => base_path() . 'libraries/mathjax/icons/mathjax.png',
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return base_path() . 'libraries/mathjax/plugin.js';
  }

}
