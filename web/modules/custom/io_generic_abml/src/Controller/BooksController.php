<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal\io_generic_abml\Controller\GenericABMLController;

use Drupal\io_generic_abml\DTOs\BookDTO;

use Drupal\io_generic_abml\DAOs\BookDAO;

class BooksController extends GenericABMLController {
  /**
   * Books all list into custom theme
   */
  public function listBooks() {
    // prepare render array
    $content = [];

    // Get all books
    $booksList = BookDAO::getAll();

    $content = [
      '#theme' => 'books_list_page',
      '#results' => $booksList['resultsDTO'],
      '#counter' => $booksList['counter'],
    ];

    return $content;
  }


}
