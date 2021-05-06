<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DAOs\GenericDAO;
use Drupal\io_generic_abml\DAOs\InstanceDAO;

use Drupal\io_generic_abml\DTOs\ItemDTO;
use Drupal\io_generic_abml\DTOs\ItemTypeDTO;
use Drupal\io_generic_abml\DTOs\EditorialDTO;
use Drupal\io_generic_abml\DTOs\AcquisitionConditionDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;


/**
 * DAO class for item entity.
 */
class ItemDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'bn_item';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'i';

  /**
   * To get multiple bn_item records.
   *
   * @param array $header
   *   The table header used to sort the results
   * @param string $search_key
   *   The search string to filter
   * @param int $limit
   *   The number of records to be fetched.
   */
  public static function getAll($search_item = NULL, $search_item_type = NULL, $limit = NULL) {
    if (!isset($limit))
      $limit = 10;
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, [
        'id',
        'title',
        'cover',
        //'item_type_id',
        'parallel_title',
        'edition',
        'publication_place',
        //'editorial_id',
        'publication_year',
        'extension',
        'dimensions',
        'others_physical_details',
        'complements',
        'serie_title',
        'serie_number',
        'notes',
        'isbn',
        'issn',
        //'acquisition_condition_id',
        'acquisition_condition_notes',
        'createdon',
        'updatedon'
      ]);

    // Add join to bn_item_type table
    $query->join('bn_item_type', 'it', 'it.id = ' . self::TABLE_ALIAS . '.item_type_id');
    $query->fields('it', ['id', 'type', 'status']);

    // Add join to bn_editorial table
    $query->leftjoin('bn_editorial', 'ed', 'ed.id = ' . self::TABLE_ALIAS . '.editorial_id');
    $query->fields('ed', ['id', 'editorial', 'status']);

    // Add join to bn_acquisition_condition table
    $query->join('bn_acquisition_condition', 'aci', 'ac.id = ' . self::TABLE_ALIAS . '.acquisition_condition_id');
    $query->fields('aci', ['id', 'condition', 'status']);

    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);

    // Now we have to check if user has selected any filter: $search_item_type
    if (isset($search_item_type)) {
      switch ($search_item_type) {
        case '0':
          # TODOS
          // If $search_key is not null means that need to add the where condition.
          if (!is_null($search_item)) {
            // Add LEFT JOIN to bn_item_author table
            $query->leftjoin('bn_item_author', 'ite_aut', 'ite_aut.article_id = ' . self::TABLE_ALIAS . '.id');
            // Add LEFT JOIN to bn_author table
            $query->leftjoin('bn_author', 'aut', 'aut.id = ite_aut.author_id');
            $group = $query->orConditionGroup()
              ->condition(self::TABLE_ALIAS . '.title', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition(self::TABLE_ALIAS . '.parallel_title', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition('aut.first_name', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition('aut.last_name', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition(self::TABLE_ALIAS . '.isbn', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition(self::TABLE_ALIAS . '.issn', "%" . Html::escape($search_item) . "%", 'LIKE');
            $query->condition($group);
          }
          break;
        case '1':
          # TITULO
          // If $search_key is not null means that need to add the where condition.
          if (!is_null($search_item)) {
            $query->condition(self::TABLE_ALIAS . '.title', "%" . Html::escape($search_item) . "%", 'LIKE')
            ->condition(self::TABLE_ALIAS . '.parallel_title', "%" . Html::escape($search_item) . "%", 'LIKE');
          }
          break;

        case '2':
          # AUTOR
          if (!is_null($search_item)) {
            // Add LEFT JOIN to bn_item_author table
            $query->leftjoin('bn_item_author', 'ite_aut', 'ite_aut.article_id = ' . self::TABLE_ALIAS . '.id');
            // Add LEFT JOIN to bn_author table
            $query->leftjoin('bn_author', 'aut', 'aut.id = ite_aut.author_id');
            $group = $query->orConditionGroup()
              ->condition('aut.first_name', "%" . Html::escape($search_item) . "%", 'LIKE')
              ->condition('aut.last_name', "%" . Html::escape($search_item) . "%", 'LIKE');
            $query->condition($group);
          }
          break;

        case '3':
          # MATERIA
          break;

        case '4':
          # ISBN
          // Add join to bn_book table
          $query->leftjoin('bn_book', 'bk', 'bk.article_id = ' . self::TABLE_ALIAS . '.id');
          $query->condition('bk.isbn', "%" . Html::escape($search_item) . "%", 'LIKE');
          break;

        default:
          # code...
          $a = 1;
          break;
      }
    }
    //$query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);
    // Add the range sentence (limit, lenght) to the query using the page number.
    $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit($limit);
    // Results array to be returned.
    $results = [];
    // Add to the results array the total rows count.
    $results = parent::addTotalCounterData($query, $results);
    // Query execution.
    $result = $query->execute()->fetchAll();
    // Add to the results the counter summary considering the pagination.
    $results = parent::addSummaryCounterData($query, $results, $limit);
    //Now we have to build the DTO list result.
    $resultsDTO = [];
    // DB results iterations
    foreach ($result as $key => $row) {
      $entityDTO = self::getItemDTOFromRecord($row);
      // Add element to result array
      array_push($resultsDTO, $entityDTO);
    }
    // Add the DTOs to the results array.
    $results['resultsDTO'] = $resultsDTO;
    return $results;
  }

  /**
   * To load an Item record.
   *
   * @param int $id
   *   The item ID.
   * @return ItemDTO $objectDTO
   *   The searched object DTO.
   */
  public static function load($id) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, [
        'id',
        'title',
        'cover',
        //'item_type_id',
        'parallel_title',
        'edition',
        'publication_place',
        //'editorial_id',
        'publication_year',
        'extension',
        'dimensions',
        'others_physical_details',
        'complements',
        'serie_title',
        'serie_number',
        'notes',
        'isbn',
        'issn',
        //'acquisition_condition_id',
        'acquisition_condition_notes',
        'createdon',
        'updatedon'
      ]);

    // Add join to bn_item_type table
    $query->join('bn_item_type', 'it', 'it.id = ' . self::TABLE_ALIAS . '.item_type_id');
    $query->fields('it', ['id', 'type', 'status']);

    // Add join to bn_editorial table
    $query->leftjoin('bn_editorial', 'ed', 'ed.id = ' . self::TABLE_ALIAS . '.editorial_id');
    $query->fields('ed', ['id', 'editorial', 'status']);

    // Add join to bn_acquisition_condition table
    $query->join('bn_acquisition_condition', 'aci', 'ac.id = ' . self::TABLE_ALIAS . '.acquisition_condition_id');
    $query->fields('aci', ['id', 'condition', 'status']);

    // Add join to bn_article_format table
    //$query->leftjoin('bn_article_format', 'af', 'af.id = ' . self::TABLE_ALIAS . '.article_format_id');
    //$query->fields('af', ['id', 'format', 'status']);

    // Get crator username
    $query = parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition(self::TABLE_ALIAS . '.id', $id, '=')->execute()->fetchObject();
    $objectDTO = self::getItemDTOFromRecord($result);

    return $objectDTO;
  }

  /**
   * Get the list of Items Types in the select format
   */
  public static function getItemsTypesSelectFormat($status = NULL, $opcion_vacia) {
    $select_options = parent::getListSelectFormat('bn_item_type', 'type', $status, $opcion_vacia);

    return $select_options;
  }

  /**
   * Get the list of Acquisition Conditions Types in the select format
   */
  public static function getAcquisitionConditionsTypesSelectFormat($status = NULL, $opcion_vacia) {
    $select_options = parent::getListSelectFormat('bn_acquisition_condition', 'condition', $status, $opcion_vacia);

    return $select_options;
  }

  /**
   * Get the list of Article Formats in the select format
   */
  public static function getArticlesFormatsSelectFormat($status = NULL, $opcion_vacia) {
    $select_options = parent::getListSelectFormat('bn_article_format', 'format', $status, $opcion_vacia);

    return $select_options;
  }

  /**
   * Get the list of Article Formats in the select format
   */
  public static function getEditorialesSelectFormat($newOption = false, $status = NULL, $opcion_vacia) {
    $query = \Drupal::database()->select('bn_editorial', 'e')
      ->fields('e', ['id', 'editorial', 'status']);
    if(isset($status))
      $query->condition('e.status', $status, '=');
    // Query execution.
    $result = $query->execute()->fetchAll();

    $select_options = [];
    //$select_options[0] = 'Seleccione un Autor';
    if ($newOption)
      $select_options[-1] = 'Crear nueva Editorial';
    foreach ($result as $key => $row) {
      $select_options[$row->id] = $row->editorial;
    }
    return $select_options;
  }

  /**
   * To insert a new record into DB.
   *
   * @param array $fields
   *   An array conating the bn_item_author data in key value pair.
   */
  public static function LinkAuthorToArticle(int $idArticle, int $idAuthor, int $userId ) {
    $fields = [
      'article_id' => $idArticle,
      'author_id' => $idAuthor,
      'createdby' => $userId,
      'createdOn' => date("Y-m-d h:m:s"),
    ];
    return \Drupal::database()->insert('bn_item_author')->fields($fields)->execute();
  }

  /** Utis methods *********************************************************************************/
  /**
   * Create a ItemDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return ItemDTO $itemDTO
   *   DTO object
   */
  private static function getItemDTOFromRecord($row) {
    $itemDTO = new ItemDTO();
    $createdBy = new UserDTO();
    $updatedBy = new UserDTO();

    // set simple fields
    $itemDTO->setId($row->id);
    $itemDTO->setTitle($row->title);

    // Item Type DGM
    if (isset($row->it_id)) {
      $itemTypeDTO = new ItemTypeDTO();
      $itemTypeDTO->setId($row->it_id);
      $itemTypeDTO->setType($row->type);
      $itemTypeDTO->setStatus($row->status);
      $itemDTO->setItemType($itemTypeDTO);
    }

    $itemDTO->setParallelTitle($row->parallel_title);
    $itemDTO->setCover($row->cover);
    $itemDTO->setEdition($row->edition);
    $itemDTO->setPublicationPlace($row->publication_place);

    // Editorial
    if (isset($row->ed_id)) {
      $editorialDTO = new EditorialDTO();
      $editorialDTO->setId($row->ed_id);
      $editorialDTO->setEditorial($row->editorial);
      $editorialDTO->setStatus($row->status);
      $itemDTO->setEditorial($editorialDTO);
    }

    $itemDTO->setPublicationYear($row->publication_year);
    $itemDTO->setExtension($row->exstension);
    $itemDTO->setDimensions($row->dimensions);
    $itemDTO->setOthersPhysicalDetails($row->others_physical_details);
    $itemDTO->setComplements($row->complements);
    $itemDTO->setSerieTitle($row->serie_title);
    $itemDTO->setSerieNumber($row->serie_number);
    $itemDTO->setNotes($row->notes);
    $itemDTO->setIsbn($row->isbn);
    $itemDTO->setIssn($row->issn);

    // Acquison Condition
    if (isset($row->aci_id)) {
      $acquisitionConditionDTO = new AcquisitionConditionDTO();
      $acquisitionConditionDTO->setId($row->aci_id);
      $acquisitionConditionDTO->setCondition($row->condition);
      $acquisitionConditionDTO->setStatus($row->status);
      $itemDTO->setAcquisitionCondition($acquisitionConditionDTO);
    }

    $itemDTO->setAcquisitionConditionNotes($row->acquision_condiiton_notes);

    // set audit fields
    $createdBy->setUid($row->createdby_uid);
    $createdBy->setUsername($row->createdby);
    $itemDTO->setCreatedBy($createdBy);

    $itemDTO->setCreatedOn($row->createdon);

    $updatedBy->setUid($row->updatedby_uid);
    $updatedBy->setUsername($row->updatedby);
    $itemDTO->setUpdatedBy($updatedBy);

    $itemDTO->setUpdatedOn($row->updatedon);

    return $itemDTO;
  }
}
