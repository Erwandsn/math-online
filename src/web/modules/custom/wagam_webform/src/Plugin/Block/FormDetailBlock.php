<?php

namespace Drupal\wagam_webform\Plugin\Block;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Url;
use Drupal\taxonomy\TermInterface;
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

  protected FileUrlGeneratorInterface $fileUrlGenerator;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, RendererInterface $renderer, FileUrlGeneratorInterface $fileUrlGenerator)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->renderer = $renderer;
    $this->fileUrlGenerator = $fileUrlGenerator;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static (
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('file_url_generator')
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
      /** @var TermInterface $branche_term */
      $branche_term = $term_storage->load($webform->getThirdPartySetting('wagam_webform', 'branche'));

      $illustration_uri = null;
      $illustration_value = $branche_term->get('field_illustration_de_fond');
      if(!$illustration_value->isEmpty()){
        $illustration_uri = $this->fileUrlGenerator->generate($illustration_value->first()->entity->get('uri')->getString())->toString();
      }

      $url = $webform->toLink()->getUrl()->toString();
      $label = NULL;
      if(isset($this->configuration['type']) && $this->configuration['type'] == 'teaser'){
        $url = Url::fromRoute('wagam.form_overview', ['webform' => $webform->id()])->toString();
        $label = $this->t("Voir l'exercice");
      }
      $link_markup = new FormattableMarkup('<div class="d-grid link-wrapper"><a href="@link" class="btn btn-green"><i class="far fa-play-circle"></i> @label</a></div>', ['@link' => $url, '@label' => $label ?? $this->t('Commencer')]);
      $niveau_markup = new FormattableMarkup('<i class="fas fa-user-graduate"></i> @label', ['@label' => $niveau_term->getName()]);
      $temps_markup = new FormattableMarkup('<i class="fas fa-clock"></i>@label minutes', ['@label' => $webform->getThirdPartySetting('wagam_webform', 'temps_estime')]);
      $branche_markup = new FormattableMarkup('<small class="branche">@term</small>', ['@term' => $branche_term->getName()]);
      $related_lessons = [];
      if(!empty($webform->getThirdPartySetting('wagam_webform', 'related_lesson'))){
        $related_lessons = $node_storage->load($webform->getThirdPartySetting('wagam_webform', 'related_lesson'));
        $viewable_lesson = $node_view_builder->view($related_lessons, 'card');
        $related_lessons = $this->renderer->render($viewable_lesson);
      }
      $build = [
        'detail_header' => [
          '#type' => 'container',
          '#attributes' => [
            'class' => [
              'detail-header',
            ],
            'style' => [
              '--background-illustration: url('. $illustration_uri .');',
              '--branche-color: '.$branche_term->get('field_code_couleur')->getString().';',
            ],
          ],
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
        ],
        'props' => [
          '#type' => 'container',
          '#attributes' => [
            'class' => [
              'form-props',
            ],
            'style' => [
              '--branche-color: '.$branche_term->get('field_code_couleur')->getString().';',
            ],
          ],
          'type-wrapper' => [
            '#type' => 'container',
            '#attributes' => [
              'class' => [
                'type-wrapper'
              ],
            ],
            'branche' => [
              "#markup" => $branche_markup->__toString(),
            ],

          ],
          'properties' => [
            '#type' => 'html_tag',
            '#tag' => 'ul',
            '#attributes' => [
              'class' => [
                'row',
                'props-detail'
              ],
            ],
            'nb_questions' => [
              '#type' => 'html_tag',
              '#tag' => 'li',
              'content' => [
                '#markup' =>  !empty($webform->get('elements')) ? $this->t('@count questions.', ['@count' => count(Yaml::decode($webform->get('elements')))]) : '',
              ],
              '#attributes' => [
                'class' => [
                  'col-6',
                  empty($webform->get('elements')) ? 'd-none' : '',
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
