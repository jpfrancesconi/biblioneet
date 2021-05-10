<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;

use Drupal\file\Entity\File;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\Request;

use Drupal\io_generic_abml\Controller\GenericABMLController;

use Drupal\io_generic_abml\DTOs\ItemDTO;

use Drupal\io_generic_abml\DAOs\ItemDAO;

use Drupal\io_generic_abml\Form\Instances\ItemInstancesTableForm;

class InstancesController extends GenericABMLController {
  /**
   * List instances of an item and manage them
   */
  public function listInstancesByItem(int $id, Request $req) {
    $content = [];
    // Check if we've received the id item parameter
    $itemDTO = null;
    if(isset($id) && $id !== 0) {
        $itemDTO = ItemDAO::load($id);
    } else {
        return;
    }

    // item instances table form
    $entity_table_form_instance = new ItemInstancesTableForm($id);
    $content['table'] = $this->formBuilder->getForm($entity_table_form_instance, $id);

    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    // now we've to display add form
    $content['add_form'] = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\Instances\InstanceForm', ['id' => $id]);

    $content['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => 'VOLVER',
      '#attributes' => ['class' => ['btn', 'btn-danger', 'btn-sm']],
      '#url' => Url::fromRoute('io_generic_abml.items.list'),
    ];

    return $content;
  }
}
