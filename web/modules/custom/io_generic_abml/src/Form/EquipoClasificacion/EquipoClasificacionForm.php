<?php
namespace Drupal\io_generic_abml\Form\EquipoClasificacion;

use Drupal;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;


use Drupal\io_generic_abml\DAOs\EquipoClasificacionDAO;
use Drupal\io_generic_abml\DTOs\EquipoClasificacionDTO;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EquipoClasificacionForm extends FormBase implements FormInterface {
  /**
   * ROUTE_TO_RETURN is the route to return at the end of the differntes form flows.
   */
  const ROUTE_TO_RETURN = 'io_generic_abml.equipo_clasificacion.list';

  /**
   * Messaenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messanger
   *  Messenger service.
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
   * { @inheritDoc }
   */
  public function getFormId() {
    return 'equipo_clasificacion_form';
  }

  /**
   * { @inheritDoc }
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $equipoClasificacion = new EquipoClasificacionDTO();
    if (!empty($id)) {
      if ($id == 'invalid') {
        $this->messenger->addError(t('Invalid record'));
        return new RedirectResponse(Drupal::url(self::ROUTE_TO_RETURN));
      }
      $equipoClasificacion = EquipoClasificacionDAO::load($id);
      $form['id'] = [
        '#type' => 'hidden',
        '#value' => $id
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
      '#title'  => t('Desde esta ventana podrá dar de alta nuevos tipos de clasificación de equipo para que sean luego asociados a las equipos.'),
    ];

    $form['clasificacion'] = [
      '#type'           => 'textfield',
      '#title'          => t('Tipo'),
      '#required'       => TRUE,
      '#default_value'  => ($equipoClasificacion) ? $equipoClasificacion->getClasificacion() : '',
      '#description'    => t('Nombre del tipo de clasificación de equipo'),
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['activo'] = [
      '#type'           => 'checkbox',
      '#title'          => t('Activo'),
      '#required'       => FALSE,
      '#default_value'  => ($equipoClasificacion) ? $equipoClasificacion->getActivo() : 1,
      '#description'    => t('Estado del tipo clasificación de equipo'),
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

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $tipo = $form_state->getValue('clasificacion');
    if (empty($tipo)) {
      $form_state->setErrorByName('clasificacion', t('Debe ingresar un nombre para el tipo de clasificacion de equipo'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

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

    $fields = [
      'clasificacion' => trim(strtoupper($form_state->getValue('clasificacion'))),
      'activo' => $form_state->getValue('activo'),
      'usuario_alta' => $user->id(),
    ];

    if (!empty($id) && EquipoClasificacionDAO::exists($id)) {
      // Set Updated audit fields
      $fields['usuario_mod'] = $user->id();
      $fields['fecha_mod'] = date("Y-m-d h:m:s");
      EquipoClasificacionDAO::update($id, $fields);
      $message = 'El tipo de clasificación de equipo [' . $form_state->getValue('clasificacion') . '] ha sido actualizado correctamente.';
    } else {
      $new_record_id = EquipoClasificacionDAO::add($fields);
      $message = 'El tipo de clasificación de equipo [' . $form_state->getValue('clasificacion') . '] ah sido creado satisfactoriamente.';
    }
    $this->messenger->addStatus($message);
  }

}
