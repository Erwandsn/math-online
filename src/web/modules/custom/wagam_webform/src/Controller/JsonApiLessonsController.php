<?php

namespace Drupal\wagam_webform\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JsonApiLessonsController
{

  public function handleAutocomplete(Request $request)
  {
    $results = [];
    $input = $request->query->get('q');
    if(!$input){
      return new JsonResponse($results);
    }

    $input = Xss::filter($input);
    $query = \Drupal::entityQuery('node')
      ->condition('type',  ['chapitre', 'lecon'], 'IN')
      ->condition('title', $input, 'CONTAINS')
      ->sort('title', 'DESC')
      ->range(0, 10);
    $ids = $query->execute();
    $nodes = $ids ? \Drupal\node\Entity\Node::loadMultiple($ids) : [];
    foreach ($nodes as $node) {
      $results[] = [
        'value' => EntityAutocomplete::getEntityLabels([$node]),
        'label' => $node->getTitle().' ('.$node->id().')',
      ];
    }

    return new JsonResponse($results);
  }
}
