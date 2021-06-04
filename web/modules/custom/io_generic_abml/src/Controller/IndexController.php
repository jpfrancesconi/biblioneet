<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal;

use Drupal\file\Entity\File;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\Request;

use Drupal\io_generic_abml\Controller\GenericABMLController;

use Drupal\io_generic_abml\DTOs\ItemDTO;

use Drupal\io_generic_abml\DAOs\ItemDAO;

use Drupal\io_generic_abml\Form\Index\IndexTreeTableForm;

class IndexController extends GenericABMLController {
    /**
     * List instances of an item and manage them
     */
    public function listIndexByItem(int $id) { 
        
        // prepare render array
        $content = [];
        
        // Check if we've received the id item parameter
        $itemDTO = null;
        if(isset($id) && $id !== 0) {
            $itemDTO = ItemDAO::load($id);
            $content['title'] = [
                '#type' => 'markup',
                '#markup' => '<h4>Gestionando indice del item: '. $itemDTO->getTitle() .' </h4>',
              ];
        } else {
            return;
        }

        // Add nre record link
        $ajax_link_attributes = [
            'attributes' => [
            'class' => ['btn', 'btn-primary'],
            'data-dialog-type' => 'modal',
            'data-dialog-options' => ['width' => 700, 'height' => 400],
            ],
        ];
        $addUrl = Url::fromRoute('io_generic_abml.items.indexes.add', ['idItem'=> $id, 'idIndex'=> null, 'js' => 'no_js'], $ajax_link_attributes);
        $content['add_new_link'] = [
            '#type' => 'link',
            '#title' => 'Nueva entrada',
            '#url' => $addUrl,
        ];
        
        
        $indice_tree_table_form_instance = new IndexTreeTableForm($id);
        $content['table'] = $this->formBuilder->getForm($indice_tree_table_form_instance, $id);

        // attach library to open modals
        $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

        return $content;
    }
}
