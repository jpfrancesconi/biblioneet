<?php
namespace Drupal\io_generic_abml\Form\FrecuenciaFechaTipo;

use Drupal;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;


use Drupal\io_generic_abml\DAOs\FrecuenciaFechaTipoDAO;
use Drupal\io_generic_abml\DTOs\FrecuenciaFechaTipoDTO;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FrecuenciaFechaTipoForm extends FormBase implements FormInterface {
  /**
   * ROUTE_TO_RETURN is the route to return at the end of the differntes form flows.
   */
  const ROUTE_TO_RETURN = 'io_generic_abml.frecuencia_fecha_tipo.list';

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
    return 'frecuencia_fecha_tipo_form';
  }

  /**
   * { @inheritDoc }
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {

    if (!empty($id)) {
      if ($id == 'invalid') {
        $this->messenger->addError(t('Invalid record'));
        return new RedirectResponse(Drupal::url(self::ROUTE_TO_RETURN));
      }
      $frecuenciaFechaTipo = FrecuenciaFechaTipoDAO::load($id);
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
      '#title'  => t('Desde esta ventana podrá dar de alta nuevas frecuencias de fecha para que sean luego asociados a las actividades.'),
    ];

    $form['frecuencia'] = [
      '#type'           => 'textfield',
      '#title'          => t('Tipo de frecuencia'),
      '#required'       => TRUE,
      '#default_value'  => ($frecuenciaFechaTipo) ? $frecuenciaFechaTipo->getFrecuencia() : '',
      '#description'    => t('Nombre del tipo de frecuencia de fechas'),
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];
    $form['funcion_calculo_cont'] = [
      '#type' => 'container',
      '#description'    => t('Nombre de la funcion con la que sa calculara la siguiente fecha y sus parámetros.'),
      '#attributes' => [
        'class' => [
          'd-flex',
          ]
        ],
    ];
    $form['funcion_calculo_cont']['funcion_calculo'] = [
      '#type'           => 'textfield',
      '#title'          => t('Funcion de calculo'),
      '#required'       => TRUE,
      '#default_value'  => ($frecuenciaFechaTipo) ? $frecuenciaFechaTipo->getFuncionCalculo() : '',
      '#field_suffix' => '(',
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['funcion_calculo_cont']['param_1'] = [
      '#type'           => 'textfield',
      '#title'          => t('1er parámetro'),
      '#required'       => FALSE,
      '#default_value'  => ($frecuenciaFechaTipo) ? $frecuenciaFechaTipo->getParam_1() : '',
      '#field_suffix' => ',',
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['funcion_calculo_cont']['param_2'] = [
      '#type'           => 'textfield',
      '#title'          => t('2do parámetro'),
      '#required'       => FALSE,
      '#default_value'  => ($frecuenciaFechaTipo) ? $frecuenciaFechaTipo->getParam_2() : '',
      '#field_suffix' => ',',
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['funcion_calculo_cont']['param_3'] = [
      '#type'           => 'textfield',
      '#title'          => t('3er parámetro'),
      '#required'       => FALSE,
      '#default_value'  => ($frecuenciaFechaTipo) ? $frecuenciaFechaTipo->getParam_3() : '',
      '#field_suffix' => ')',
      '#attributes' => [
        'autocomplete' => 'off',
      ],
    ];

    $form['activo'] = [
      '#type'           => 'checkbox',
      '#title'          => t('Activo'),
      '#required'       => FALSE,
      '#default_value'  => ($frecuenciaFechaTipo) ? $frecuenciaFechaTipo->getActivo() : 1,
      '#description'    => t('Estado del tipo de trabajo'),
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
      'frecuencia' => trim(strtoupper($form_state->getValue('frecuencia'))),
      'funcion_calculo' => trim($form_state->getValue('funcion_calculo')),
      'param_1' => trim($form_state->getValue('param_1')),
      'param_2' => trim($form_state->getValue('param_2')),
      'param_3' => trim($form_state->getValue('param_3')),
      'activo' => $form_state->getValue('activo'),
      'usuario_alta' => $user->id(),
    ];

    if (!empty($id) && FrecuenciaFechaTipoDAO::exists($id)) {
      // Set Updated audit fields
      $fields['usuario_mod'] = $user->id();
      $fields['fecha_mod'] = date("Y-m-d h:m:s");
      FrecuenciaFechaTipoDAO::update($id, $fields);
      $message = 'La frecuencia de fecha [' . $form_state->getValue('frecuencia') . '] ha sido actualizada correctamente.';
    } else {
      $new_record_id = FrecuenciaFechaTipoDAO::add($fields);
      $message = 'La frecuencia de fecha [' . $form_state->getValue('frecuencia') . '] ah sido creada satisfactoriamente.';
    }
    $this->messenger->addStatus($message);
  }

}
