<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DAOs\GenericDAO;

use Drupal\io_generic_abml\DTOs\ItemDTO;
use Drupal\io_generic_abml\DTOs\InstanceDTO;
use Drupal\io_generic_abml\DTOs\InstanceStatusDTO;
use Drupal\io_generic_abml\DTOs\EditorialDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;


/**
 * DAO class for instance entity.
 */
class InstanceDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'bn_instance';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'ins';

  /**
   * To insert a new record into DB.
   *
   * @param array $fields
   *   An array conating the author data in key value pair.
   */
  public static function add(array $fields) {
    return \Drupal::database()->insert(self::TABLE_NAME)->fields($fields)->execute();
  }

  /**
   * To get multiple Tipos de Equipos records.
   *
   * @param array $header
   *   The table header used to sort the results
   * @param string $search_key
   *   The search string to filter
   * @param int $limit
   *   The number of records to be fetched.
   */
  public static function getInstancesFromItem($header, $idItem = NULL ,$limit = NULL) {
    $limit = 99;
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', 'inv_code', 'signature', 'item_id', 'createdon', 'updatedon']);
    // Add join to bn_instance_status table
    $query->join('bn_instance_status', 'is', 'is.id = ' . self::TABLE_ALIAS . '.instance_status_id');
    $query->fields('is', ['id', 'status_name', 'status', 'lendable']);
    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);
    // If $search_key is not null means that need to add the where condition.
    if (!is_null($idItem)) {
      $query->condition(self::TABLE_ALIAS. '.instance_status_id', $idItem, '=');
    }
    // Add the orderBy sentences to the query using the header.
    $query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);
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
      $entityDTO = self::getInstanceDTOFromRecord($row);
      // Add element to result array
      array_push($resultsDTO, $entityDTO);
    }
    // Add the DTOs to the results array.
    $results['resultsDTO'] = $resultsDTO;
    return $results;
  }

  /**
   * To load an Instance record.
   *
   * @param int $id
   *   The instance ID.
   * @return ItemDTO $objectDTO
   *   The searched object DTO.
   */
  public static function load($id) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', 'inv_code', 'signature', 'item_id', 'createdon', 'updatedon']);
    // Add join to bn_instance_status table
    $query->join('bn_instance_status', 'is', 'is.id = ' . self::TABLE_ALIAS . '.instance_status_id');
    $query->fields('is', ['id', 'status_name', 'status', 'lendable']);
    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition(self::TABLE_ALIAS . '.id', $id, '=')->execute()->fetchObject();
    $objectDTO = self::getInstanceDTOFromRecord($result);

    return $objectDTO;
  }

  /**
   * Get the list of Items Types in the select format
   */
  public static function getInstanceStatusSelectFormat($status = NULL, $opcion_vacia) {
    $select_options = parent::getListSelectFormat('bn_instance_status', 'status_name', $status, $opcion_vacia);

    return $select_options;
  }

  /** Utis methods *********************************************************************************/
  /**
   * Create a InstanceDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return ItemDTO $itemDTO
   *   DTO object
   */
  private static function getInstanceDTOFromRecord($row) {
    $instanceDTO = new InstanceDTO();
    $createdBy = new UserDTO();
    $updatedBy = new UserDTO();

    // set simple fields
    $instanceDTO->setId($row->id);
    $instanceDTO->setInvCode($row->inv_code);
    $instanceDTO->setSignature($row->signature);

    // set Item which instance belong to
    if (isset($row->item_id)) {
      $itemDTO = new ItemDTO();
      $itemDTO->setId($row->item_id);
      $instanceDTO->setItem($itemDTO);
    }

    // set Item which instance belong to
    if (isset($row->is_id)) {
      $instanceStatusDTO = new InstanceStatusDTO();
      $instanceStatusDTO->setId($row->is_id);
      $instanceStatusDTO->setStatusName($row->status_name);
      $instanceStatusDTO->setStatus($row->status);
      $instanceStatusDTO->setLendable($row->lendable);
      $instanceDTO->setInstanceStatus($instanceStatusDTO);
    }

    // set audit fields
    $createdBy->setUid($row->createdby_uid);
    $createdBy->setUsername($row->createdby);
    $instanceDTO->setCreatedBy($createdBy);

    $instanceDTO->setCreatedOn($row->createdon);

    $updatedBy->setUid($row->updatedby_uid);
    $updatedBy->setUsername($row->updatedby);
    $instanceDTO->setUpdatedBy($updatedBy);

    $instanceDTO->setUpdatedOn($row->updatedon);

    return $instanceDTO;
  }

}
