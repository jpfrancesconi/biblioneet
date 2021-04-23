<?php
namespace Drupal\io_generic_abml\Form\Articles;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBuilderInterface;

use Drupal\file\Entity\File;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\io_generic_abml\DAOs\ArticleDAO;
use Drupal\io_generic_abml\DAOs\BookDAO;
use Drupal\io_generic_abml\DAOs\MagazineDAO;
use Drupal\io_generic_abml\DAOs\MultimediaDAO;

class ArticleForm extends FormBase {
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
    return 'article_form';
  }

  /**
   * Build the add new article form.
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
    // Get selected type of article
    if (!empty($form_state->getValue('article_type_id'))) {
      // Use a default value.
      $selected_article_type = $form_state->getValue('article_type_id');
    } else {
      $selected_article_type = '0';
    }

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Desde este formulario se podran crear nuevos artículos'),
    ];

    // Field: bn_article.title
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Título'),
      '#default_value' => '',
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Título del artículo',
      ],
    ];

    // Field: bn_article.inv_code
    $form['inv_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Inventario'),
      '#default_value' => '',
      '#attributes' => [
        'placeholder' => 'Código de inventario del artículo',
      ],
    ];

    // Field: cover
    $form['upload']['cover'] = [
      '#type'               => 'managed_file',
      '#upload_location'    => 'public://articles_images/',
      '#multiple'           => FALSE,
      '#upload_validators'  => [
        'file_validate_extensions'  => ['png gif jpg jpeg jfif'],
        'file_validate_size'        => [51200000],
        //'file_validate_image_resolution' => array('800x600', '400x300'),.
      ],
      '#title'              => $this->t('Foto o Imagen del artículo'),
      '#default_value'      => null,//($authorDTO) ? [$authorDTO->getPicture()] : '',
    ];

    // Field: bn_article.article_type_id
    $articlesTypesOptions = ArticleDAO::getArticlesTypesSelectFormat(true);//[0=> 'LIBRO', 1=>'REVISTA'];
    $form['article_type_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Tipo de artículo'),
      '#default_value' => '',
      '#options' => $articlesTypesOptions,
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Tipo de artículo',
      ],
      '#ajax' => [
        'callback' => '::getTypeFormCallback',
        'wrapper' => 'type-form-container',
        'event' => 'change',
      ],
    ];

    // Field: bn_article.article_format_id
    $articlesFormatOptions = ArticleDAO::getArticlesFormatsSelectFormat(true);//[0 => 'FISICO', 1 => 'DIGITAL'];
    $form['article_format_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Formato del artículo'),
      '#default_value' => '',
      '#options' => $articlesFormatOptions,
      '#required' => TRUE,
      '#attributes' => [
        'placeholder' => 'Formato del artículo',
      ],
    ];

    // Create instances for this article
    $form['instances'] = [
      '#type' => 'number',
      '#title' => $this->t('Instancias del artículo'),
      '#description' => $this->t('Indique la cantidad de copias que tiene en existencia del articulo'),
      '#default_value' => 0,
      '#required' => FALSE,
      '#attributes' => [
        'placeholder' => 'Instancias del artículo',
      ],
    ];

    // Form type main container - all ajax rebuild is happend here
    $form['type_form_container'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'type-form-container'],
    ];
    // If user has selected an option we need to show all fields for that option
    if($selected_article_type && $selected_article_type != "0") {
      switch ($selected_article_type) {
        case '1':
          //Libro
          $form['type_form_container']['type_form_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Complete todos los datos requeridos del Libro'),
          ];
          // ISBN Field
          $form['type_form_container']['type_form_fieldset']['isbn'] = [
            '#type' => 'textfield',
            '#title' => $this->t('ISBN'),
            '#require' => TRUE,
            '#attributes' => [
              'placeholder' => $this->t('ISBN'),
            ],
          ];
          // Editorial Field
          $editorialesFormatOptions = ArticleDAO::getEditorialesSelectFormat(true);
          $form['type_form_container']['type_form_fieldset']['editorial_id'] = [
            '#type' => 'select',
            '#title' => $this->t('Editorial'),
            '#default_value' => '',
            '#options' => $editorialesFormatOptions,
            '#required' => FALSE,
            '#attributes' => [
              'placeholder' => 'Editorial del Libro',
            ],
          ];
          // anio_edicion Field
          $form['type_form_container']['type_form_fieldset']['anio_edicion'] = [
            '#type' => 'number',
            '#title' => $this->t('Año de edición'),
            '#require' => FALSE,
            '#attributes' => [
              'placeholder' => $this->t('Año de edición'),
            ],
            '#pattern' => '[dddd]',
          ];
          // cant_paginas Field
          $form['type_form_container']['type_form_fieldset']['cant_paginas'] = [
            '#type' => 'number',
            '#title' => $this->t('Cantidad de páginas'),
            '#require' => FALSE,
            '#attributes' => [
              'placeholder' => $this->t('Cantidad de páginas'),
            ],
            '#pattern' => '[0-99999]',
          ];
          // idioma Field
          $idiomasOptions = ['ESPAÑOL'=> 'ESPAÑOL', 'INGLES' => 'INGLES', 'ITALIANO' => 'ITALIANO', 'FRANCES' => 'FRANCES', 'PORTUGUES' => 'PORTUGUES', 'ALEMAN' => 'ALEMAN', 'CHINO' => 'CHINO', 'RUSO' => 'RUSO'];
          $form['type_form_container']['type_form_fieldset']['idioma'] = [
            '#type' => 'select',
            '#title' => $this->t('Idioma'),
            '#default_value' => '',
            '#options' => $idiomasOptions,
            '#required' => FALSE,
            '#attributes' => [
              'placeholder' => 'Idioma',
            ],
          ];
          break;
        case '2':
          //Book
          $form['type_form_container']['type_form_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Complete todos los datos requeridos de la revista'),
          ];
          // Numero Field
          $form['type_form_container']['type_form_fieldset']['numero'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Número'),
            '#require' => TRUE,
            '#attributes' => [
              'placeholder' => $this->t('Número'),
            ],
          ];
          break;
        case '3':
          //Multimedia
          $form['type_form_container']['type_form_fieldset'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Complete todos los datos requeridos del Multimedia'),
          ];
          // ISBN Field
          $form['type_form_container']['type_form_fieldset']['description'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Descripción'),
            '#require' => TRUE,
            '#attributes' => [
              'placeholder' => $this->t('Descripción'),
            ],
          ];
          break;
        default:
          #
          break;
      }
    } else {
      // Add an empty message
      $form['type_form_container']['type_form_fieldset'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Seleccione un tipo de artículo para completar el registro'),
      ];
    }
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('GUARDAR'),
    ];
    // If all required fields are not completed we can't submit the form yet.
    //$form['actions']['submit']['#disabled'] = TRUE;
    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('CANCELAR'),
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
    $title = $form_state->getValue('title');
    if (strlen($title) < 5 || strlen($title) > 150) {
      // Set an error for the form element with a key of "title".
      $form_state->setErrorByName('title', $this->t('El tìtulo debe contener al menos dos caracteres y como maximo 150 caracteres.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $trigger = (string) $form_state->getTriggeringElement()['#value'];
    if ($trigger == 'GUARDAR') {
      $title = $form_state->getValue('title');
      $user = \Drupal::currentUser();

      // Cover image
      $file_usage = Drupal::service('file.usage');
      $cover_fid = NULL;
      $image = $form_state->getValue('cover');
      if (!empty($image)) {
        $cover_fid = $image[0];
      }

      $fields = [
        'title' => trim(strtoupper($form_state->getValue('title'))),
        'cover' => $cover_fid,
        'inv_code' => trim($form_state->getValue('inv_code')),
        'article_type_id' => $form_state->getValue('article_type_id'),
        'article_format_id' => $form_state->getValue('article_format_id'),
        'createdby' => $user->id(),
      ];

      // Check if we have to create instances
      $instances = $form_state->getValue('instances');
      if($instances != 0) {
        //Save the new article into DB
        $new_record_id = ArticleDAO::add($fields, $instances);
      } else {
        //Save the new article into DB
        $new_record_id = ArticleDAO::add($fields);
      }

      if ($cover_fid) {
        $file = File::load($cover_fid);
        $file->setPermanent();
        $file->save();
        $file_usage->add($file, 'article', 'file', $new_record_id);
      }
      switch ($form_state->getValue('article_type_id')) {
        case '1':
          // Book case
          $extraFields = [
            'isbn' => trim(strtoupper($form_state->getValue('isbn'))),
            'editorial_id' => $form_state->getValue('editorial_id'),
            'anio_edicion' => $form_state->getValue('anio_edicion'),
            'cant_paginas' => $form_state->getValue('cant_paginas'),
            'idioma' => $form_state->getValue('idioma'),
            'article_id' => $new_record_id,
            'createdby' => $user->id(),
            'createdon' => date("Y-m-d h:m:s"),
          ];
          BookDAO::add($extraFields);
          break;
        case '2':
          // Magazine case
          $extraFields = [
            'numero' => trim(strtoupper($form_state->getValue('isbn'))),
            'article_id' => $new_record_id,
            'createdby' => $user->id(),
            'createdon' => date("Y-m-d h:m:s"),
          ];
          MagazineDAO::add($extraFields);
          break;
        case '3':
          // Multimedia case
          $extraFields = [
            'description' => trim(strtoupper($form_state->getValue('description'))),
            'article_id' => $new_record_id,
            'createdby' => $user->id(),
            'createdon' => date("Y-m-d h:m:s"),
          ];
          MultimediaDAO::add($extraFields);
          break;
        default:
          break;
      }

      $this->messenger()->addStatus($this->t('El artìculo %title fue creado satisfactoriamente.', ['%title' => $title]));
    } else {
        // Rebuild the form. This causes buildForm() to be called again before the
        // associated Ajax callback. Allowing the logic in buildForm() to execute
        // and update the $form array so that it reflects the current state of
        // the instrument family select list.
        $form_state->setRebuild();
    }
  }

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
    return $form['type_form_container'];
  }
}

