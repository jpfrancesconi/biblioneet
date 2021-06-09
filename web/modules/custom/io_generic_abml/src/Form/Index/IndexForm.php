<?php

namespace Drupal\io_generic_abml\Form\Index;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\file\Entity\File;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\io_generic_abml\DTOs\IndexDTO;

use Drupal\io_generic_abml\DAOs\IndexDAO;

/**
 * Entity Form.
 */
class IndexForm extends FormBase implements FormInterface {
  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * EntityForm constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'index_form_add';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $idItem = NULL, $idIndex = NULL) {
    $indexDTO = null;
    $form['itid'] = [
        '#type' => 'hidden',
        '#value' => $idItem,
    ];
    if ($idIndex) {
      $indexDTO = IndexDAO::load($idIndex);
      $form['ixid'] = [
        '#type' => 'hidden',
        '#value' => $indexDTO->getId(),
      ];
    }
    $form['#attributes']['novalidate'] = '';

    // Subtitle
    $form['subtitle'] = [
      '#type'   => 'item',
      '#title'  => t('Desde esta ventana podrá dar de alta entradas de indices.'),
    ];

    $indxPadres = IndexDAO::getIndexTreeSelect($idItem);

    // Select.
    $form['index_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Indice Padre'),
      '#options' => $indxPadres,
      '#default_value' => ($indexDTO && $indexDTO->getIndexPadre()) ? $indexDTO->getIndexPadre()->getId() : 0,
      '#empty_option' => $this->t('-SELECCIONAR UNA OPCION-'),
      '#description' => $this->t('Indice Padre del que se esta registrando.'),
    ];

    $form['content'] = [
      '#type'           => 'textfield',
      '#title'          => t('Contenido'),
      '#required'       => TRUE,
      '#default_value'  => ($indexDTO) ? $indexDTO->getContent() : '',
      '#description'    => t('Contenido del indice'),
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['number'] = [
        '#type'           => 'textfield',
        '#title'          => t('Numero'),
        '#required'       => FALSE,
        '#default_value'  => ($indexDTO) ? $indexDTO->getNumber() : '',
        '#description'    => t('Numero del indice'),
        '#attributes' => [
          'autocomplete' => 'off',
        ],
      ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type'   => 'submit',
      '#value'  => 'Guardar',
      '#attributes' => ['class' => ['btn', 'btn-success']],
    ];

    $form['actions']['cancel'] = [
      '#type'       => 'link',
      '#title'      => 'Volver',
      '#attributes' => ['class' => ['btn', 'btn-danger']],
      '#url'        => Url::fromRoute('io_generic_abml.items.indexes.list', ['id' => $idItem]),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $id = $form_state->getValue('aid');
    //$this->messenger->addError('Hello world');

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get current user operator
    $user = \Drupal::currentUser();

    $idIndex = $form_state->getValue('ixid');
    $idItem = $form_state->getValue('itid');

    $index_id = $form_state->getValue('index_id');
    if($index_id === "" || $index_id === 0)
      $index_id = null;
    
    $number = $form_state->getValue('number');
      if($number === "" || $indexnumber_id === 0)
        $number = null;
    
    $fields = [
      'content' => $form_state->getValue('content'),
      'number' => $number,
      'index_id' => $index_id,
      'item_id' => $idItem,
      'peso' => 0,
      'createdby' => $user->id(),
    ];
    if (!empty($idIndex) && IndexDAO::exists($idIndex)) {
      //$indexDTO = IndexDAO::load($id);

      // Set Updated auditory fields
      $fields['updatedby'] = $user->id();
      $fields['updatedon'] = date("Y-m-d h:m:s");

      IndexDAO::update($idIndex, $fields);
      $message = 'El indice ha sido actualizadoa correctamente.';
    } else {
      $id = IndexDAO::add($fields);

      // $this->dispatchEmployeeWelcomeMailEvent($new_employee_id);
      $message = 'El indice ha sido creado satisfactoriamente.';
    }
    $this->messenger->addStatus($message);
    //$form_state->setRedirect('io_equipos_locs.localizaciones.edit',
    //  ['js' => 'no_ajax', 'localizacion_id' => $id]);
  }
}