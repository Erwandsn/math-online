<?php

namespace Drupal\wagam_webform\Plugin\Block;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\webform\Entity\Webform;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a block with Dashboad - Sidebard Menu.
 *
 * @Block(
 *   id = "wagam_form_detail",
 *   admin_label = @Translation("WaGamMaths - Bloc Affichant les détails d'un formulaire"),
 *   category = @Translation("WaGamMaths"),
 * )
 */
class FormDetailBlock extends BlockBase implements ContainerFactoryPluginInterface
{

  protected EntityTypeManagerInterface $entityTypeManager;

  protected RendererInterface $renderer;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, RendererInterface $renderer)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->renderer = $renderer;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static (
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('renderer')
    );
  }

  /**
   * @inheritDoc
   */
  public function getCacheContexts()
  {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url']);
  }

  /**
   * @inheritDoc
   */
  public function build(){
    $build = [];



    if(isset($this->configuration['webform']) && !empty($this->configuration['webform'])){
      /** @var Webform $webform */
      $webform = $this->configuration['webform'];
      $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
      $node_storage = $this->entityTypeManager->getStorage('node');
      $node_view_builder = $this->entityTypeManager->getViewBuilder('node');

      $niveau_term = $term_storage->load($webform->getThirdPartySetting('wagam_webform', 'niveau_cible'));

      $link_markup = new FormattableMarkup('<div class="d-grid"><a href="@link" class="btn btn-green"><i class="far fa-play-circle"></i> @label</a></div>', ['@link' => $webform->toLink()->getUrl()->toString(), '@label' => $this->t('Commencer')]);
      $niveau_markup = new FormattableMarkup('<i class="fas fa-user-graduate"></i> @label', ['@label' => $niveau_term->getName()]);
      $temps_markup = new FormattableMarkup('<i class="fas fa-clock"></i>@label minutes', ['@label' => $webform->getThirdPartySetting('wagam_webform', 'temps_estime')]);
      $related_lessons = [];
      if(!empty($webform->getThirdPartySetting('wagam_webform', 'related_lesson'))){
        $related_lessons = $node_storage->load($webform->getThirdPartySetting('wagam_webform', 'related_lesson'));
        $viewable_lesson = $node_view_builder->view($related_lessons, 'card');
        $related_lessons = $this->renderer->render($viewable_lesson);
      }
      $build = [
        'title'=> [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#attributes' => [
            'class' => [
              'form_title',
            ]
          ],
          'content' => [
            '#markup' => $webform->get('title'),
          ]
        ],
        'properties' => [
          '#type' => 'html_tag',
          '#tag' => 'ul',
          '#attributes' => [
            'class' => [
              'form-props',
              'row',
            ],
          ],
          'nb_questions' => [
            '#type' => 'html_tag',
            '#tag' => 'li',
            'content' => [
              '#markup' => $this->t('@count questions.', ['@count' => count(Yaml::decode($webform->get('elements')))])
            ],
            '#attributes' => [
              'class' => [
                'col-6',
              ],
            ],
          ],
          'niveau' => [
            '#type' => 'html_tag',
            '#tag' => 'li',
            'content' => [
              '#markup' => $niveau_markup->__toString(),
            ],
            '#attributes' => [
              'class' => [
                'col-6',
              ],
            ],
          ],
          'temps' => [
            '#type' => 'html_tag',
            '#tag' => 'li',
            'content' => [
              '#markup' => $temps_markup->__toString(),
            ],
            '#attributes' => [
              'class' => [
                'col-6',
              ],
            ],
          ],
          'link' => [
            '#markup' => $link_markup->__toString(),
          ],
        ],


        'related_lesson' => [
          'title' => [
            '#markup' => '<h2 class="related-title">'.$this->t('Les cours associés').'</h2>',
          ],
          'content' => [
            '#type' => 'container',
            '#attributes' => [
            'class' => [
                'row',
                'lesson-wrapper'
              ],
            ],
            'items' => [
              "#markup" => $related_lessons,
            ],
          ],
        ],
      ];

    }

    return $build;
  }
}
