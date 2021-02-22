<?php

namespace Drupal\biblioneet\Controller;

use Drupal\biblioneet\Form\AuthorTableForm;
use Drupal;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\file\Entity\File;

use Drupal\biblioneet\DAOs\AuthorDAO;

/**
 * Authors Controller class.
 */
class AuthorsController extends ControllerBase {
  /**
   * The Form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * Databse Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $db;

  /**
   * Request.
   *
   * @var Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * Constructs the AuthorsController.
   *
   * @param \Drupal\Core\Form\FormBuilder $form_builder
   *   The Form builder.
   * @param \Drupal\Core\Database\Connection $con
   *   The database connection.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request
   *   Request stack.
   */
  public function __construct(FormBuilder $form_builder,
    Connection $con,
    RequestStack $request) {
    $this->formBuilder = $form_builder;
    $this->db = $con;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('form_builder'),
        $container->get('database'),
        $container->get('request_stack')
      );
  }

  /**
   * Lists all the authors.
   */
  public function listAuthors() {
    // prepare render array
    $content = [];

    // add author search form
    $content['search_form'] =
      $this->formBuilder->getForm('Drupal\biblioneet\Form\AuthorSearchForm');

    // get current search parameter on the request
    $search_key = $this->request->getCurrentRequest()->get('search');

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
      return new RedirectResponse(Drupal::url('biblioneet.author.list'));
    } else {
      // Get author by ID
      $author = AuthorDAO::loadWithProperties($author);


      $pic = File::load($author->picture);
      if ($pic) {
        $pic_url = file_create_url($pic->getFileUri());
      } else {
        $module_handler = Drupal::service('module_handler');
        $path = $module_handler->getModule('biblioneet')->getPath();
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
        '#url' => Url::fromRoute('biblioneet.author.edit', ['author' => $author->id]),
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
        '#url' => Url::fromRoute('biblioneet.author.delete.modal.form', ['id' => $author->id, 'js' => 'ajax']),
      ];
      // Attach the library for pop-up dialogs/modals.
      $content['#attached']['library'][] = 'core/drupal.dialog.ajax';

      if ($js == 'ajax') {
        $modal_title = t('Autor #@id', ['@id' => $author->id]);
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
        $content['#author'] = $author;

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
    $deleteForm = $this->formBuilder->getForm('Drupal\biblioneet\Form\AuthorDeleteForm', $id);

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
