<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;

use Drupal\file\Entity\File;

use Drupal\io_generic_abml\Controller\GenericABMLController;

use Drupal\io_generic_abml\DTOs\ItemDTO;

use Drupal\io_generic_abml\DAOs\ItemDAO;

class ItemsController extends GenericABMLController {
  /**
   * List items into a custom theme
   */
  public function listItems() {
    global $base_url;
    // prepare render array
    $content = [];

    // Display search form
    $searchForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\Items\ItemsSearchForm');
    // get current search parameter on the request
    $search_item_type = $this->request->getCurrentRequest()->get('search_item_type');
    $search_item = $this->request->getCurrentRequest()->get('search_item');

    // Declare array to contain all request results
    $rows = [];
    // Declare array to contain all request results counter
    $rowsCounter = -1;
    // Check if users has typed something into search textbox field
    if (!empty($search_item)) {
      $rowsCounter = 0;
      // Get all items by filters
      $itemsList = [];
      $itemsList = ItemDAO::getAll($search_item, $search_item_type);
      $rowsCounter = $itemsList['counter'];

      // iterate on items result list
      foreach ($itemsList['resultsDTO'] as $key => $itemDTO) {
        $picture = FALSE;
        if ($itemDTO->getCover())
          $picture = File::load($itemDTO->getCover());

        if ($picture) {
          $style = Drupal::entityTypeManager()->getStorage('image_style')->load('mobile_1x_560px_');
          $picture_url = $style->buildUrl($picture->getFileUri());
        } else {
          $module_handler = Drupal::service('module_handler');
          $path = $module_handler->getModule('io_generic_abml')->getPath();
          $picture_url = $base_url . '/' . $path . '/assets/' . $itemDTO->getItemType()->getType() . '.png';
        }

        $rows[$itemDTO->getId()] = [
          'id' => sprintf("%05s", $itemDTO->getId()),
          'picture' => [
            'data' => [
              '#type' => 'html_tag',
              '#tag' => 'img',
              '#attributes' => ['src' => $picture_url],
            ],
          ],
          'title' => $itemDTO->getTitle(),
          'edition' => ($itemDTO->getEdition() !== null) ? $itemDTO->getEdition() : "",
          'publicationPlace' => ($itemDTO->getPublicationPlace() !== null) ? $itemDTO->getPublicationPlace() : "",
          'editorial' => ($itemDTO->getEditorial() !== null) ? $itemDTO->getEditorial()->getEditorial() : "",
          'publicationYear' => ($itemDTO->getPublicationYear() !== null) ? $itemDTO->getPublicationYear() : "",
          'itemType' => $itemDTO->getItemType()->getType(),
          'available' => ItemDAO::getItemAvalability($itemDTO->getId(), 1),
          'not_available' => ItemDAO::getItemAvalability($itemDTO->getId(), 0),
          'edit_item' => true,
          //'itemFormat' => $itemDTO->getItemFormat()->getFormat(),
        ];
      }
    }

    $content = [
      '#theme' => 'items_list_page',
      '#search_form' => $searchForm,
      '#results' => $rows, //$itemsList['resultsDTO'],
      '#results_counter' => $rowsCounter, //$itemsList['counter']
      '#pager' => [
        '#type' => 'pager',
      ],
    ];

    return $content;
  }
}
