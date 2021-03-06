<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;

use Drupal\file\Entity\File;

use Drupal\io_generic_abml\Controller\GenericABMLController;

use Drupal\io_generic_abml\DTOs\ArticleDTO;

use Drupal\io_generic_abml\DAOs\ArticleDAO;

class ArticlesController extends GenericABMLController {
  /**
   * Books all list into custom theme
   */
  public function listArticles() {
    global $base_url;
    // prepare render array
    $content = [];

    // Get all books
    $articlesList = ArticleDAO::getAll();

    // iterate on articles result list
    $rows = [];
    foreach ($articlesList['resultsDTO'] as $key => $articleDTO) {
      $picture = FALSE;
      if ($articleDTO->getCover())
        $picture = File::load($articleDTO->getPicture());

      if ($picture) {
        $style = Drupal::entityTypeManager()->getStorage('image_style')->load('mobile_1x_560px_');
        $picture_url = $style->buildUrl($picture->getFileUri());
      } else {
        $module_handler = Drupal::service('module_handler');
        $path = $module_handler->getModule('io_generic_abml')->getPath();
        $picture_url = $base_url . '/' . $path . '/assets/' . $articleDTO->getArticleType()->getType() . '.png';
      }
      // Editorial
      $editorial = '';
      $className = (new \ReflectionClass($articleDTO))->getShortName();
      if(strcmp($className, "BookDTO") === 0) {
        $editorial = $articleDTO->getEditorial();
      }
      $rows[$articleDTO->getId()] = [
        'id' => sprintf("%04s", $articleDTO->getId()),
        'picture' => [
          'data' => [
            '#type' => 'html_tag',
            '#tag' => 'img',
            '#attributes' => ['src' => $picture_url],
          ],
        ],
        'title' => $articleDTO->getTitle(),
        'editorial' => $editorial,
        'articleType' => $articleDTO->getArticleType()->getType(),
        'articleFormat' => $articleDTO->getArticleFormat()->getFormat(),
      ];
    }

    $content = [
      '#theme' => 'articles_list_page',
      '#results' => $rows,//$articlesList['resultsDTO'],
      '#counter' => $articlesList['counter'],
    ];

    return $content;
  }


}
