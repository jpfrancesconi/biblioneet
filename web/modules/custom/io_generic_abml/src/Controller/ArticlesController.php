<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal\io_generic_abml\Controller\GenericABMLController;

use Drupal\io_generic_abml\DTOs\ArticleDTO;

use Drupal\io_generic_abml\DAOs\ArticleDAO;

class ArticlesController extends GenericABMLController {
  /**
   * Books all list into custom theme
   */
  public function listArticles() {
    // prepare render array
    $content = [];

    // Get all books
    $articlesList = ArticleDAO::getAll();

    $content = [
      '#theme' => 'articles_list_page',
      '#results' => $articlesList['resultsDTO'],
      '#counter' => $articlesList['counter'],
    ];

    return $content;
  }


}
