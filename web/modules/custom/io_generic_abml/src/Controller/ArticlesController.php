<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;

use Drupal\file\Entity\File;

use Drupal\io_generic_abml\Controller\GenericABMLController;

use Drupal\io_generic_abml\DTOs\ArticleDTO;

use Drupal\io_generic_abml\DAOs\ArticleDAO;

class ArticlesController extends GenericABMLController {
  /**
   * List all instances form an article
   *
   * @param Integer $articleId
   * @return RenderArray
   */
  public function listInstances($articleId) {
    $articleDTO = ArticleDAO::load($articleId);
    $content = [
      '#type' => 'markup',
      '#markup' => $articleDTO->getTitle(),
    ];
    return $content;
  }

  /**
   * Books all list into custom theme
   */
  public function listArticles() {
    global $base_url;
    // prepare render array
    $content = [];

    // Display search form
    $searchForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\Articles\ArticlesSearchForm');
    // get current search parameter on the request
    $search_article_type = $this->request->getCurrentRequest()->get('search_article_type');
    $search_article = $this->request->getCurrentRequest()->get('search_article');

    // Declare array to contain all request results
    $rows = [];
    // Declare array to contain all request results counter
    $rowsCounter = -1;
    // Check if users has typed something into search textbox field
    if(!empty($search_article)){
      $rowsCounter = 0;
      // Get all articles by filters
      $articlesList = [];
      $articlesList = ArticleDAO::getAll($search_article, $search_article_type);
      $rowsCounter = $articlesList['counter'];

      // iterate on articles result list
      foreach ($articlesList['resultsDTO'] as $key => $articleDTO) {
        $picture = FALSE;
        if ($articleDTO->getCover())
        $picture = File::load($articleDTO->getCover());

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
        if (strcmp($className, "BookDTO") === 0) {
          $editorial = ($articleDTO->getEditorial() !== null) ? $articleDTO->getEditorial()->getEditorial() : "";
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
    }

    $content = [
      '#theme' => 'articles_list_page',
      '#search_form' => $searchForm,
      '#results' => $rows, //$articlesList['resultsDTO'],
      '#results_counter' => $rowsCounter, //$articlesList['counter']
      '#pager' => [
        '#type' => 'pager',
      ],
    ];

    return $content;
  }


}
