<?php
namespace Drupal\io_generic_abml\Form\FallaTipo;

use Drupal;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\io_generic_abml\DAOs\EquipoTiposDAO;
use Drupal\io_generic_abml\DAOs\FallaTipoDAO;
use Drupal\io_generic_abml\DAOs\RegimenTipoDAO;
use Drupal\io_generic_abml\DTOs\FallaTipoDTO;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FallaTipoForm extends FormBase implements FormInterface {
  /**
   * ROUTE_TO_RETURN is the route to return at the end of the differntes form flows.
   */
  const ROUTE_TO_RETURN = 'io_generic_abml.falla_tipo.list';

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
    return 'falla_tipo_form';
  }

  /**
   * { @inheritDoc }
   */
  public function buildForm(array $form, FormStateInterface $form_state, $equipo_tipo_id = NULL, $id = NULL) {

    if (!empty($id)) {
      if ($id == 'invalid') {
        $this->messenger->addError(t('Invalid record'));
        return new RedirectResponse(Drupal::url(self::ROUTE_TO_RETURN));
      }
      $fallaTipo = FallaTipoDAO::load($id);
      $form['id'] = [
        '#type' => 'hidden',
        '#value' => $id
      ];
    }
    if (!empty($equipo_tipo_id)) {
      // Load the EquipoTipoDTO to get data form the tipo de equipo related
      $tipoEquipoDTO = EquipoTiposDAO::load($equipo_tipo_id);

      $form['tipo_equipo_id'] = [
        '#type' => 'hidden',
        '#value' => $equipo_tipo_id
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
      '#title'  => t('Desde esta ventana podrá dar de alta nuevos tipos de falla para que sean luego asociados a las actividades.'),
    ];

    $form['tipo_equipo'] = [
      '#type'           => 'textfield',
      '#title'          => t('Tipo de equipo'),
      '#required'       => FALSE,
      '#disabled'       => TRUE,
      '#default_value'  => $tipoEquipoDTO->getTipo(),
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['falla'] = [
      '#type'           => 'textfield',
      '#title'          => t('Tipo de Falla'),
      '#required'       => TRUE,
      '#default_value'  => ($fallaTipo) ? $fallaTipo->getFalla() : '',
      '#description'    => t('Nombre del tipo de falla'),
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['activo'] = [
      '#type'           => 'checkbox',
      '#title'          => t('Activo'),
      '#required'       => FALSE,
      '#default_value'  => ($fallaTipo) ? $fallaTipo->getActivo() : 1,
      '#description'    => t('Estado del tipo de falla'),
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
      $response->addCommand(
        new RedirectCommand(
          Url::fromRoute(self::ROUTE_TO_RETURN, ['equipo_tipo_id' => $form_state->getValue('tipo_equipo_id')])->toString()
        )
      );
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
      'falla' => strtoupper($form_state->getValue('falla')),
      'tipo_equipo_id' => $form_state->getValue('tipo_equipo_id'),
      'activo' => $form_state->getValue('activo'),
      'usuario_alta' => $user->id(),
    ];

    if (!empty($id) && FallaTipoDAO::exists($id)) {
      // Set Updated audit fields
      $fields['usuario_mod'] = $user->id();
      $fields['fecha_mod'] = date("Y-m-d h:m:s");
      FallaTipoDAO::update($id, $fields);
      $message = 'El tipo de falla [' . $form_state->getValue('falla') . '] ha sido actualizada correctamente.';
    } else {
      $new_record_id = FallaTipoDAO::add($fields);
      $message = 'El tipo de falla [' . $form_state->getValue('falla') . '] ah sido creada satisfactoriamente.';
    }
    $this->messenger->addStatus($message);
    $form_state->setRedirect(self::ROUTE_TO_RETURN, ['equipo_tipo_id' => $form_state->getValue('tipo_equipo_id')]);
  }

}
