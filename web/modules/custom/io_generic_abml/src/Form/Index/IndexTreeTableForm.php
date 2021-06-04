<?php

namespace Drupal\io_generic_abml\Form\Index;

use Drupal;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

use Drupal\Core\Render\RendererInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Database\Connection;

use Drupal\file\Entity\File;

use Drupal\io_generic_abml\DAOs\IndexDAO;

/**
 * Entity list in tableselect format.
 */
class IndexTreeTableForm extends FormBase implements FormInterface {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'index_tree_table_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $id = null) {
        // attach library to open modals
        $form['#attached'] = ['library' => ['core/drupal.dialog.ajax']];

        $form['itid'] = [
            '#type' => 'hidden',
            '#value' => $id,
        ];

        $form['table-row'] = [
            '#type' => 'table',
            '#header' => [
                $this->t('Texto'),
                $this->t('Numero'),
                $this->t('Operaciones'),
                $this->t('Peso'),
                $this->t('Padre'),
            ],
            '#empty' => $this->t('No se han encontrado registros'),
            // TableDrag: Each array value is a list of callback arguments for
            // drupal_add_tabledrag(). The #id of the table is automatically
            // prepended; if there is none, an HTML ID is auto-generated.
            '#tabledrag' => [
                [
                'action' => 'match',
                'relationship' => 'parent',
                'group' => 'row-pid',
                'source' => 'row-id',
                'hidden' => TRUE, /* hides the WEIGHT & PARENT tree columns below */
                'limit' => FALSE,
                ],
                [
                'action' => 'order',
                'relationship' => 'sibling',
                'group' => 'row-weight',
                ],
            ],
            '#attributes' => [
                'id' => 'table',
                'class' => ['table-sm','io-table-sm']
            ],
        ];  
        
        $resultsDTO = IndexDAO::getIndexTreeByItemId($id);

        foreach ($resultsDTO as $row) {
            $indexDTO = $row;

            $ajax_link_attributes = [
                'attributes' => [
                  'class' => 'use-ajax',
                  'data-dialog-type' => 'modal',
                  'data-dialog-options' => ['width' => 700, 'height' => 400],
                ],
            ];
            $editUrl = Url::fromRoute('io_generic_abml.items.indexes.edit', ['idItem' => $indexDTO->getItem()->getId(), 'idIndex' => $indexDTO->getId(), 'js' => 'no_js'], [
                'attributes' => [
                  'class' => '',
                  'data-dialog-type' => 'modal',
                  'data-dialog-options' => ['width' => 700, 'height' => 400],
                ],
            ]);
            $quickEditLink = \Drupal::service('link_generator')->generate(t('<i class="fas fa-edit"></i>'), $editUrl);
    
            // prepare delete link
            $deletetUrl = Url::fromRoute('io_generic_abml.items.indexes.delete.getmodal', ['idIndex' => $indexDTO->getId(), 'js' => 'ajax'], $ajax_link_attributes);
            $deleteLink = \Drupal::service('link_generator')->generate(t('<i class="far fa-trash-alt"></i>'), $deletetUrl);
    
            // TableDrag: Mark the table row as draggable.
            $form['table-row'][$indexDTO->getId()]['#attributes']['class'][] = 'draggable';

            // We can add the 'tabledrag-root' class to a row in order to indicate
            // that the row may not be nested under a parent row.  In our sample data
            // for this example, the description for the item with id '11' flags it as
            // a 'root' item which should not be nested.
            if ($indexDTO->getIndexPadre() == null) {
                $form['table-row'][$indexDTO->getId()]['#attributes']['class'][] = 'tabledrag-root';
            }

            // TableDrag: Sort the table row according to its existing/configured
            // weight.
            $form['table-row'][$indexDTO->getId()]['#weight'] = $indexDTO->getPeso();

            // Indent item on load.
            $peso = $indexDTO->getPeso();
            if (isset($peso) && $indexDTO->getPeso() > 0) {
                $indentation = [
                '#theme' => 'indentation',
                '#size' => $indexDTO->getPeso(),
                ];
            }

            // Some table columns containing raw markup.
            $form['table-row'][$indexDTO->getId()]['content'] = [
                '#markup' => $indexDTO->getContent(),
                '#prefix' => !empty($indentation) ? \Drupal::service('renderer')->render($indentation) : '',
            ];

            // Some table columns containing raw markup.
            $form['table-row'][$indexDTO->getId()]['number'] = [
                '#markup' => $indexDTO->getNumber(),
            ];

            // Some table columns containing raw markup.
            if(IndexDAO::esHoja($indexDTO->getId())){
                $operationLinks = t('@linkEdit @linkDelete', array('@linkEdit' => $quickEditLink, '@linkDelete' => $deleteLink));
            } else {
                if ($indexDTO->getIndexPadre() == null) {
                    $operationLinks = t('');
                } else {
                    $operationLinks = t('@linkEdit', array('@linkEdit' => $quickEditLink));
                }
            }

            $form['table-row'][$indexDTO->getId()]['op'] =  [
                '#markup' =>$operationLinks,
            ];

            // This is hidden from #tabledrag array (above).
            // TableDrag: Weight column element.
            $form['table-row'][$indexDTO->getId()]['weight'] = [
                '#type' => 'weight',
                '#title' => $this->t('Weight for ID @id', ['@id' => $indexDTO->getId()]),
                '#title_display' => 'invisible',
                '#default_value' => $indexDTO->getPeso(),
                // Classify the weight element for #tabledrag.
                '#attributes' => [
                'class' => ['row-weight'],
                ],
            ];
            $form['table-row'][$indexDTO->getId()]['parent']['id'] = [
                '#parents' => ['table-row', $indexDTO->getId(), 'id'],
                '#type' => 'hidden',
                '#value' => $indexDTO->getId(),
                '#attributes' => [
                  'class' => ['row-id'],
                ],
            ];
            $form['table-row'][$indexDTO->getId()]['parent']['pid'] = [
                '#parents' => ['table-row', $indexDTO->getId(), 'pid'],
                '#type' => 'number',
                '#size' => 3,
                '#min' => 0,
                '#title' => $this->t('Parent ID'),
                '#default_value' => $indexDTO->getIndexPadre() != null ? $indexDTO->getIndexPadre()->getId() : null ,
                '#attributes' => [
                  'class' => ['row-pid'],
                ],
            ];
        }

        $form['actions'] = ['#type' => 'actions'];
        $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Guardar los cambios'),
        '#attributes' => ['class' => ['btn', 'btn-success']],
        ];
        $form['actions']['cancel'] = [
        '#type' => 'submit',
        '#value' => 'Cancelar',
        '#attributes' => [
            'title' => $this->t('Return to TableDrag Overview'),
        ],
        '#submit' => ['::cancel'],
        '#attributes' => ['class' => ['btn', 'btn-danger']],
        ];

        return $form;
    }

    /**
     * Form submission handler for the 'Return to' action.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function cancel(array &$form, FormStateInterface $form_state) {
        $idItem = $form_state->getValue('itid');
        $form_state->setRedirect('io_generic_abml.items.indexes.list', ['id' => $idItem]);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {}

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        // Because the form elements were keyed with the item ids from the database,
        // we can simply iterate through the submitted values.
        $submissions = $form_state->getValue('table-row');
        IndexDAO::updateIndexTree($submissions);
    }

}