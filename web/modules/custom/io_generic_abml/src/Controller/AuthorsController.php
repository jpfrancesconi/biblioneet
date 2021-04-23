<?php

namespace Drupal\io_generic_abml\Controller;

use Drupal\io_generic_abml\Form\Autor\AuthorTableForm;
use Drupal;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

use Drupal\file\Entity\File;

use Drupal\io_generic_abml\DAOs\AuthorDAO;

/**
 * Authors Controller class.
 */
class AuthorsController extends GenericABMLController {

  /**
   * Lists all the authors.
   */
  public function listAuthors() {
    // prepare render array
    $content = [];

    // add author search form
    $content['search_form'] =
      $this->formBuilder->getForm('Drupal\io_generic_abml\Form\Autor\AuthorSearchForm');

    // get current search parameter on the request
    $search_key = $this->request->getCurrentRequest()->get('search');

    // Add nre record link
    $addUrl = Url::fromRoute('io_generic_abml.author.add');
    $content['add_new_link'] = [
        '#type' => 'link',
        '#title' => 'Nuevo Autor',
        '#attributes' => [
          'class' => ['btn', 'btn-primary'],
        ],
        '#url' => $addUrl,
    ];

    $author_table_form_instance =
      new AuthorTableForm($this->db, $search_key);
    $content['table'] =
      $this->formBuilder->getForm($author_table_form_instance);
    $content['pager'] = [
      '#type' => 'pager',
    ];

    // attach library to open modals
    $content['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

    return $content;
  }

  /**
   * To view an employee details.
   */
  public function viewAuthor($author, $js = 'nojs') {
    global $base_url;

    if ($author == 'invalid') {
      drupal_set_message(t('Invalid author record'), 'error');
      return new RedirectResponse(Drupal::url('io_generic_abml.author.list'));
    } else {
      // Get author by ID
      $authorDTO = AuthorDAO::load($author);


      $pic = $authorDTO->getPicture();
      if(isset($pic))
        $pic = File::load($authorDTO->getPicture());
      if ($pic) {
        $pic_url = file_create_url($pic->getFileUri());
      } else {
        $module_handler = Drupal::service('module_handler');
        $path = $module_handler->getModule('io_generic_abml')->getPath();
        $pic_url = $base_url . '/' . $path . '/assets/profile_placeholder.png';
      }
      $content['#image'] = [
        '#type' => 'html_tag',
        '#tag' => 'img',
        '#attributes' => ['src' => $pic_url, 'height' => 400],
      ];

      $content['#edit'] = [
        '#type' => 'link',
        '#title' => 'Editar',
        '#attributes' => ['class' => ['btn btn-primary']],
        '#url' => Url::fromRoute('io_generic_abml.author.edit', ['author' => $authorDTO->getId()]),
      ];
      $content['#delete'] = [
        '#type' => 'link',
        '#title' => 'Eliminar',
        '#attributes' => [
          'class' => [
            'use-ajax',
            'btn btn-danger',
          ],
        ],
        '#url' => Url::fromRoute('io_generic_abml.author.delete.modal.form', ['id' => $authorDTO->getId(), 'js' => 'ajax']),
      ];
      // Attach the library for pop-up dialogs/modals.
      $content['#attached']['library'][] = 'core/drupal.dialog.ajax';

      if ($js == 'ajax') {
        $modal_title = t('Autor #@id', ['@id' => $authorDTO->getId()]);
        $options = [
          'dialogClass' => 'popup-dialog-class',
          'width' => '70%',
          'height' => '80%',
        ];
        $response = new AjaxResponse();
        $response->addCommand(new OpenModalDialogCommand(
          $modal_title,
          $content,
          $options
        ));
        return $response;
      } else {
        $content['#theme'] = 'authors_details_page';
        $content['#author'] = $authorDTO;

        return $content;
      }
    }
  }

  /**
   *  Display Delete modal form
   */
  function getDeleteModalForm($id, $js) {
    // If we have not user as parameter we have to show an error situation
    if(!isset($id))
      return;

    // Instantiate the Author Delete Form
    $deleteForm = $this->formBuilder->getForm('Drupal\io_generic_abml\Form\Autor\AuthorDeleteForm', $id);

    // Check if we have to use modal or not
    if($js == 'ajax') {
      $response = new AjaxResponse();
      $response->addCommand(new OpenModalDialogCommand('Eliminar autor seleccionado', $deleteForm, ['widht' => '80%', 'text-align' => 'center']));
      return $response;
    } else {
      return $deleteForm;
    }
  }

}
