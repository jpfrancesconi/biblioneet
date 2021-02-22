<?php

namespace Drupal\io_generic_abml\Form\EquipoTipos;

use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\ReplaceCommand;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\io_generic_abml\DAOs\EquipoTiposDAO;
use Drupal\io_generic_abml\DTOs\EquipoTipoDTO;


/**
 * Entity Form.
 */
class EquipoTipoForm extends FormBase implements FormInterface {
  /**
   * ROUTE_TO_RETURN is the route to return at the end of the differntes form flows.
   */
  const ROUTE_TO_RETURN = 'io_generic_abml.equipo_tipo.list';
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
    return 'equipo_tipo_add';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $equipo_tipo_id = NULL) {
    $equipoTipo = new EquipoTipoDTO();
    if ($equipo_tipo_id) {
      if ($equipo_tipo_id == 'invalid') {
        $this->messenger->addError(t('Invalid record'));
        return new RedirectResponse(Drupal::url('io_generic_abml.equipo_tipo.list'));
      }
      $equipoTipo = EquipoTiposDAO::load($equipo_tipo_id);
      $form['id'] = [
        '#type' => 'hidden',
        '#value' => $equipoTipo->getId(),
      ];
    }
    $form['#attributes']['novalidate'] = '';

     // Preffix and suffix to wrapp the form
    $form['#prefix'] = '<div id="modal_form">';
    $form['#suffix'] = '</div>';
    // The status messages that will contain any form errors.
    $form['status_messages'] = [
      '#type' => 'status_messages',
      '#weight' => -10,
    ];
    // Subtitle
    $form['subtitle'] = [
      '#type'   => 'item',
      '#title'  => t('Desde esta ventana podrá dar de alta nuevos tipos de equipos para que sean luego asociados a los equipos.'),
    ];

    $form['tipo'] = [
      '#type'           => 'textfield',
      '#title'          => t('Tipo de Equipo'),
      '#required'       => TRUE,
      '#default_value'  => ($equipoTipo) ? $equipoTipo->getTipo() : '',
      '#description'    => t('Nombre del tipo de equipo'),
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['activo'] = [
      '#type'           => 'checkbox',
      '#title'          => t('Activo'),
      '#required'       => FALSE,
      '#default_value'  => ($equipoTipo) ? $equipoTipo->getActivo() : 1,
      '#description'    => t('Estado del tipo de equipo'),
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['guardar'] = [
      '#type'   => 'submit',
      '#value'  => 'Guardar',
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'submitModalFormAjax'],
        'event' => 'click',
      ],
    ];

    $form['actions']['cancel'] = [
      '#type'       => 'link',
      '#title'      => 'Cancelar',
      '#attributes' => ['class' => ['button']],
      '#url'        => Url::fromRoute(self::ROUTE_TO_RETURN),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // $tipo = $form_state->getValue('tipo');
    // if (empty($tipo)) {
    //   $form_state->setErrorByName('tipo', t('Debe ingresar un nombre para el tipo de equipo'));
    // }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
     $this->commonSubmit($form, $form_state);
     $form_state->setRedirect(self::ROUTE_TO_RETURN);
  }

  /**
   * Ajax from submit.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return void
   */
  public function submitModalFormAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#modal_form', $form));
    }
    else {
      $this->commonSubmit($form, $form_state);
      $form_state->setRedirect(self::ROUTE_TO_RETURN);
      $response->addCommand(new RedirectCommand(Url::fromRoute(self::ROUTE_TO_RETURN)->toString()));
    }
    return $response;
  }
  /**
   * Submit used by both submits (Ajax and not ajax).
   *
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function commonSubmit(array &$form, FormStateInterface $form_state) {
    // Get current user operator
    $user = \Drupal::currentUser();
    $id = $form_state->getValue('id');
    //CAMBIAR tipo
    $fields = [
      'tipo' => trim(strtoupper($form_state->getValue('tipo'))),
      'activo' => $form_state->getValue('activo'),
      'usuario_alta' => $user->id(),
    ];
    //CAMBIAR DAO
    if (!empty($id) && EquipoTiposDAO::exists($id)) {
      // Set Updated audit fields
      $fields['usuario_mod'] = $user->id();
      $fields['fecha_mod'] = date("Y-m-d h:m:s");
      EquipoTiposDAO::update($id, $fields);
      $message = 'El Tipo de Equipo [' . $form_state->getValue('tipo') . '] ha sido actualizado correctamente.';
    } else {
      $new_record_id = EquipoTiposDAO::add($fields);
      $message = 'El Tipo de Equipo [' . $form_state->getValue('tipo') . '] ah sido creado satisfactoriamente.';
    }
    $this->messenger->addStatus($message);
  }
}
