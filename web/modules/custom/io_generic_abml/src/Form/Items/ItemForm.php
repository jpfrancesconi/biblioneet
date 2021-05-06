<?php

namespace Drupal\io_generic_abml\Form\Items;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Url;
use Drupal\Component\Render\FormattableMarkup;

use Drupal\file\Entity\File;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\io_generic_abml\DAOs\AuthorDAO;
use Drupal\io_generic_abml\DAOs\ItemDAO;

use Drupal\io_generic_abml\DTOs\AuthorDTO;

class ItemForm extends FormBase {
  /**
   * Form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $form_builder;

  /**
   * {@inheritdoc}
   */
  public function __construct(FormBuilderInterface $form_builder) {
    $this->form_builder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }
  /**
   * Getter method for Form ID.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'item_form';
  }

  /**
   * Build the add new item form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Desde este formulario se podran crear nuevos ítems o editar los ya registrados'),
    ];

    // 1- Area de titulo y mencion de responsabilidad
    $form['area_1'] = [
      '#type' => 'details',
      '#title' => $this->t('Área de título y mención de responsabilidad.'),
      '#open' => TRUE,
    ];
    // Field: bn_item.title
    $form['area_1']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Título'),
      '#default_value' => '',
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Título del ítem',
      ],
    ];
    // Field: bn_item.item_type_id
    $itemsTypesOptions = ItemDAO::getItemsTypesSelectFormat(true, 'Seleccione un tipo de ítem'); //[0=> 'LIBRO', 1=>'REVISTA'];
    $form['area_1']['item_type_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Tipo de ítem (DGM)'),
      '#default_value' => '',
      '#options' => $itemsTypesOptions,
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Tipo de ítem',
      ],
      // '#ajax' => [
      //   'callback' => '::getTypeFormCallback',
      //   'wrapper' => 'type-form-container',
      //   'event' => 'change',
      // ],
    ];
    // Field: bn_item.parallel_title
    $form['area_1']['parallel_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Título paralelo'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Título paralelo del ítem',
      ],
    ];
    //Authors block BEGIN **********************************************************
    // Gather the authors selected list in the form.
    $authors_selected_list = ($form_state->get('authors_selected_list') ? $form_state->get('authors_selected_list') : null);

    $form['#tree'] = TRUE;
    $form['area_1']['authors_fieldset'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Listado de autores del item'),
      '#prefix' => '<div id="authors-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    $form['area_1']['authors_fieldset']['authors_selected_table'] = [
      '#type' => 'table',
      //'#caption' => $this->t('Lista de autores del libro'),
      '#header' => [$this->t('Nombre'), $this->t('Apellido'), $this->t('')],
      //'#rows' => $authors_selected_list,
      '#empty' => $this->t('Seleccione el o los autores o autoras del item.'),
      //'#description' => $this->t('Estos autores se vincularan con el libro que esta dando de alta.'),
      '#attributes' => [
        'id' => 'table',
        'class' => ['table-sm']
      ],
    ];
    // fill table with selected authors
    $ajax_link_attributes = [
      'attributes' => [
        'class' => 'use-ajax',
        'data-dialog-type' => 'modal',
        'data-dialog-options' => ['width' => 700, 'height' => 400],
      ],
    ];
    if(isset($authors_selected_list)) {
      foreach ($authors_selected_list as $key => $author) {
        // Check if author is created or new insert
        if ($author->getId() === 0) {
          $authorid = -1 * $key;
        } else {
          $authorid = $author->getId();
        }
        // prepare delete link
        $deletetUrl = Url::fromRoute('io_generic_abml.items.list', ['id' => $authorid, 'js' => 'ajax'], $ajax_link_attributes);
        $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);
        $operationLinks = t('@deleteLink', ['@deleteLink' => $deleteLink]);
        $form['area_1']['authors_fieldset']['authors_selected_table'][$authorid]['author_fisrtname'] = [
          '#plain_text' => $author->getFirstName(),
        ];
        $form['area_1']['authors_fieldset']['authors_selected_table'][$authorid]['author_lastname'] = [
          '#plain_text' => $author->getLastName(),
        ];
        //$deleteButtonValue = new FormattableMarkup('<i class="far fa-trash-alt '. $key .'"></i>@text', ['@text' => 'Quitar',]);
        $form['area_1']['authors_fieldset']['authors_selected_table'][$authorid]['actions'] = [
          '#type' => 'submit',
          '#value' => 'Quitar', //$deleteButtonValue,
          '#name' => 'Quitar_' . $key,
          '#ajax' => [
            'callback' => '::addmoreCallback',
            'wrapper' => 'authors-fieldset-wrapper',
          ],
          '#attributes' => [
            'class' => ['btn btn-danger btn-sm'],
            'data-author' => $key,
          ],
        ];
      }
    }

    // get authors
    $authosFormatOptions = AuthorDAO::getAuthorsSelectFormat(true, 'Seleccione un Autor');
    $form['area_1']['authors_fieldset']['author_selector'] = [
      '#type' => 'select2',
      '#default_value' => '',
      '#options' => $authosFormatOptions,
      // '#select2' => [
      //   'allowClear' => FALSE,
      // ],
      '#attributes' => [
        //define static name and id so we can easier select it
        //'id' => 'author' . $i,
        'name' => 'author_selector',
      ],
    ];
    $form['area_1']['authors_fieldset']['new_author'] = [
      '#type' => 'textfield',
      '#default_value' => '',
      '#attributes' => [
        'placeholder' => 'Ingrese: NOMBRE, APELLIDO',
      ],
      '#states' => [
        //show this textfield only if the radio 'other' is selected above
        'visible' => [
          //don't mistake :input for the type of field. You'll always use
          //:input here, no matter whether your source is a select, radio or checkbox element.
          ':input[name="author_selector"]' => ['value' => '-1'],
        ],
      ],
    ];
    $form['area_1']['authors_fieldset']['actions']['add_author'] = [
      '#type' => 'submit',
      '#value' => $this->t('Agregar autor'),
      //'#submit' => [$this, 'addAuthor'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'authors-fieldset-wrapper',
      ],
      '#attributes' => [
        'class' => ['btn btn-success btn-sm'],
        //'data-author' => $key,
      ],
      '#states' => [
        //show this textfield only if the radio 'other' is selected above
        'invisible' => [
          //don't mistake :input for the type of field. You'll always use
          //:input here, no matter whether your source is a select, radio or checkbox element.
          ':input[name="author_selector"]' => ['value' => '0'],
          ':input[name="author_selector"]' => ['value' => ''],
        ],
      ],
    ];
    //Authors block END **********************************************************

    // 2- Area de Edición
    $form['area_2'] = [
      '#type' => 'details',
      '#title' => $this->t('Área de edición.'),
    ];
    // Field: bn_item.edition
    $form['area_2']['edition'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Edición'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Edición del ítem',
      ],
    ];

    // 4- Area de publicación
    $form['area_4'] = [
      '#type' => 'details',
      '#title' => $this->t('Área de publicación.'),
    ];
    // Field: bn_item.publication_place
    $form['area_4']['publication_place'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Lugar de publicación'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Lugar de publicación del ítem',
      ],
    ];
    // Editorial Field
    $editorialesFormatOptions = ItemDAO::getEditorialesSelectFormat(true, true, 'Seleccione una editorial');
    $form['area_4']['editorial_id'] = [
      '#type' => 'select2',
      '#title' => $this->t('Editorial'),
      '#default_value' => '',
      '#options' => $editorialesFormatOptions,
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Editorial del Libro',
        'name' => 'editorial_id',
      ],
    ];
    $form['area_4']['new_editorial'] = [
      '#type' => 'textfield',
      '#default_value' => '',
      '#attributes' => [
        'placeholder' => 'NUEVA EDITORIAL',
      ],
      '#states' => [
        'visible' => [
          ':input[name="editorial_id"]' => ['value' => '-1'],
        ],
      ],
    ];
    // Field: bn_item.publication_year
    $form['area_4']['publication_year'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fecha de publicación'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Fecha de publicación del ítem',
      ],
    ];

    // 5- Area de descripcición física
    $form['area_5'] = [
      '#type' => 'details',
      '#title' => $this->t('Área de descripcición física.'),
    ];
    // Field: bn_item.extension
    $form['area_5']['extension'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Extensión del ítem'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Extensión del ítem',
      ],
    ];
    // Field: bn_item.dimensions
    $form['area_5']['dimensions'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Dimensiones del ítem'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Dimensiones del ítem',
      ],
    ];
    // Field: bn_item.others_physical_details
    $form['area_5']['others_physical_details'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Detalles físicos del ítem'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Detalles físicos del ítem',
      ],
    ];
    // Field: bn_item.complements
    $form['area_5']['complements'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Material complementario del ítem'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Material complementario del ítem',
      ],
    ];

    // 6- Area de la serie
    $form['area_6'] = [
      '#type' => 'details',
      '#title' => $this->t('Área de la serie.'),
    ];
    // Field: bn_item.serie_title
    $form['area_6']['serie_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Título de la colección del ítem'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Título de la colección del ítem',
      ],
    ];
    // Field: bn_item.serie_number
    $form['area_6']['serie_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Numeración de la serie del ítem'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Numeración de la serie del ítem',
      ],
    ];

    // 7- Area de notas
    $form['area_7'] = [
      '#type' => 'details',
      '#title' => $this->t('Área de las notas.'),
    ];
    // Field: bn_item.notes
    $form['area_7']['notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Notas del ítem'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Notas del ítem',
      ],
    ];

    // 8- Area de número normalizado y condiciones de adquisición
    $form['area_8'] = [
      '#type' => 'details',
      '#title' => $this->t('Área de número normalizado y condiciones de adquisición.'),
    ];
    // ISBN Field
    $form['area_8']['isbn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ISBN'),
      '#require' => FALSE,
      '#attributes' => [
        'placeholder' => $this->t('ISBN'),
      ],
    ];
    $form['area_8']['issn'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ISSN'),
      '#require' => FALSE,
      '#attributes' => [
        'placeholder' => $this->t('ISSN'),
      ],
    ];
    // Field: bn_item.acquisition_condition_id
    $acquisitionConditionsTypesOptions = ItemDAO::getAcquisitionConditionsTypesSelectFormat(true, 'Seleccione una condición de adquisición'); //[0=> 'LIBRO', 1=>'REVISTA'];
    $form['area_8']['acquisition_condition_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Condición de adquisición'),
      '#default_value' => '',
      '#options' => $acquisitionConditionsTypesOptions,
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Condición de adquisición',
      ],
    ];
    // Field: bn_item.acquisition_condition_notes
    $form['area_8']['acquisition_condition_notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Notas de la condición de adquisición del ítem'),
      '#default_value' => '',
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Notas de la condición de adquisición del ítem',
      ],
    ];

    // 9- Area de informacion extra
    $form['area_9'] = [
      '#type' => 'details',
      '#title' => $this->t('Información adicional sobre el ítem.'),
    ];
    // Field: cover
    $form['area_9']['upload']['cover'] = [
      '#type'               => 'managed_file',
      '#upload_location'    => 'public://items_images/',
      '#multiple'           => FALSE,
      '#upload_validators'  => [
        'file_validate_extensions'  => ['png gif jpg jpeg jfif'],
        'file_validate_size'        => [51200000],
        //'file_validate_image_resolution' => array('800x600', '400x300'),.
      ],
      '#title'              => $this->t('Foto o Imagen del ítem'),
      '#default_value'      => null, //($authorDTO) ? [$authorDTO->getPicture()] : '',
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('GUARDAR'),
      '#attributes' => [
        'class' => ['btn btn-success btn-sm'],
      ],
    ];
    // If all required fields are not completed we can't submit the form yet.
    //$form['actions']['submit']['#disabled'] = TRUE;

    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => 'CANCELAR',
      '#attributes' => ['class' => ['btn', 'btn-danger', 'btn-sm']],
      '#url' => Url::fromRoute('io_generic_abml.items.list'),
    ];

    return $form;
  }

  /**
   * Implements form validation.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $trigger = (string) $form_state->getTriggeringElement()['#value'];
    $formValues = $form_state->getValues();

    if (strlen($formValues['area_1']['title']) < 5 || strlen($formValues['area_1']['title']) > 255) {
      // Set an error for the form element with a key of "title".
      $form_state->setErrorByName('title', $this->t('El tìtulo debe contener al menos 2 caracteres y como maximo 255 caracteres.'));
    }
    if ($formValues['area_1']['item_type_id'] === "0") {
      $form_state->setErrorByName('item_type_id', $this->t('Debe seleccionar un tipo de ítem.'));
    }

    // Validate if we're adding a new author
    if ($trigger === "Agregar autor") {
      $author_selected_option = $form_state->getUserInput()['author_selector'];
      if ($author_selected_option === "-1") {
        $newAuthor = $formValues['area_1']['authors_fieldset']['new_author'];
        if ($newAuthor === "")
          $form_state->setErrorByName('authors_fieldset', $this->t('Debe seleccionar los autores correctamente.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $trigger = (string) $form_state->getTriggeringElement()['#value'];
    if ($trigger == 'GUARDAR') {
      $user = \Drupal::currentUser();

      // Cover image
      $image = $form_state->getUserInput()['area_9']['upload']['cover'];

      if (!empty($image) && $image['fids'] !== "") {
        $cover_fid = $image['fids'];
      } else {
        $cover_fid = null;
      }

      $fieldsItem = [
        'title' => trim(strtoupper($form_state->getUserInput()['area_1']['title'])),
        'cover' => $cover_fid,
        'item_type_id' => $form_state->getUserInput()['area_1']['item_type_id'],
        'createdby' => $user->id(),
      ];

    } else { //Ajax callbacks reactions
      if ($trigger == 'Agregar autor') {
        $selected_authors_list = $form_state->get('authors_selected_list');
        if(!isset($selected_authors_list))
          $selected_authors_list = [];
        // Get the new author to add
        if($form_state->getUserInput()['author_selector'] === '-1'){
          $selected_new_author_names = explode(",", $form_state->getUserInput()['area_1']['authors_fieldset']['new_author']);
          $selected_new_author = new AuthorDTO();
          $selected_new_author->setId(0);
          $selected_new_author->setFirstName(trim($selected_new_author_names[0]));
          $selected_new_author->setLastName(trim($selected_new_author_names[1]));
          $selected_author_data = $selected_new_author;

        } else {
          $selected_author_id = $form_state->getUserInput()['author_selector'];
          $selected_author_data = AuthorDAO::load($selected_author_id);
        }

        array_push($selected_authors_list, $selected_author_data);
        $form_state->set('authors_selected_list', $selected_authors_list);
      } else if ($trigger == 'Quitar') {
        $selected_authors_list = $form_state->get('authors_selected_list');
        $authorId = $form_state->getTriggeringElement()['#attributes']['data-author'];

        unset($selected_authors_list[$authorId]);
        $form_state->set('authors_selected_list', $selected_authors_list);
      }
      // Rebuild the form. This causes buildForm() to be called again before the
      // associated Ajax callback. Allowing the logic in buildForm() to execute
      // and update the $form array so that it reflects the current state of
      // the instrument family select list.
      $form_state->setRebuild();
    }
  }

  /** Callbacks section *****************************************************/
  /**
   * Ajax callback for Article type select field.
   *
   * This callback will occur *after* the form has been rebuilt by buildForm().
   * Since that's the case, the $form array should contain the right fields for
   * the form that reflect the current value of the article type selected
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The portion of the render structure that will replace the
   *   instrument-dropdown-replace form element.
   */
  public function getTypeFormCallback(array $form, FormStateInterface $form_state) {
    return $form['area_1'];
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['area_1']['authors_fieldset'];
  }

}
