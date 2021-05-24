<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DAOs\GenericDAO;

use Drupal\io_generic_abml\DTOs\AuthorDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;
use Drupal\io_generic_abml\DTOs\NationalityDTO;

/**
 * DAO class for author entity.
 */
class AuthorDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'bn_author';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'a';

  /**
   * To get multiple authors records.
   *
   * @param int $limit
   *   The number of records to be fetched.
   * @param string $orderBy
   *   The field on which the sorting to be performed.
   * @param string $order
   *   The sorting order. Default is 'DESC'.
   */
  public static function getAll($limit = NULL, $orderBy = NULL, $order = 'DESC') {
    $query = \Drupal::database()->select('bn_author', 'a')
      ->fields('a');
    if ($limit) {
      $query->range(0, $limit);
    }
    if ($orderBy) {
      $query->orderBy($orderBy, $order);
    }
    $result = $query->execute()
      ->fetchAll();
    return $result;
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
  public static function getAll2($search_key = NULL, $limit = NULL) {
    $limit = 15;
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', 'first_name', 'last_name', 'picture', 'nationality', 'description', 'status', 'createdon', 'updatedon']);

    // Add join to bn_countries table
    $query->leftjoin('bn_countries', 'c', 'c.id = a.nationality');
    $query->fields('c', ['id', 'en_short_name', 'nationality']);

    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);
    // If $search_key is not null means that need to add the where condition.
    if (!is_null($search_key)) {
      $query->condition(self::TABLE_ALIAS . '.last_name', "%" . Html::escape($search_key) . "%", 'LIKE');
    }
    // Add the orderBy sentences to the query using the header.
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
      $entityDTO = self::getAuthorDTOFromRecord($row);
      // Add element to result array
      array_push($resultsDTO, $entityDTO);
    }
    // Add the DTOs to the results array.
    $results['resultsDTO'] = $resultsDTO;
    return $results;
  }

  /**
   * To check if an author is valid.
   *
   * @param int $id
   *   The author ID.
   */
  public static function exists($id) {
    $result = \Drupal::database()->select('bn_author', 'a')
      ->fields('a', ['id'])
      ->condition('id', $id, '=')
      ->execute()
      ->fetchField();
    return (bool) $result;
  }

  /**
   * To load an author record.
   *
   * @param int $id
   *   The author ID.
   */
  public static function load($id) {

    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', 'first_name', 'last_name', 'picture', 'nationality', 'description', 'status', 'createdon', 'updatedon']);

    // Add join to bn_countries table
    $query->leftjoin('bn_countries', 'c', 'c.id = a.nationality');
    $query->fields('c', ['id', 'en_short_name', 'nationality']);

    // Get crator username
    $query = parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition(self::TABLE_ALIAS.'.id', $id, '=')->execute()->fetchObject();
    $authorDTO = self::getAuthorDTOFromRecord($result);

    return $authorDTO;
  }

  /**
   * To load an author record with joins.
   *
   * @param int $id
   *   The author ID.
   */
  public static function loadWithProperties($id) {
    $query = \Drupal::database()->select('bn_author', 'a');
    $query->leftjoin('bn_countries', 'c', 'c.id = a.nationality');
    $query->fields('a', ['id', 'first_name', 'last_name', 'picture', 'nationality', 'status', 'description', 'createdby', 'createdon', 'updatedby', 'updatedon']);
    //$query->fields('a');
    $query->fields('c', ['id', 'en_short_name']);
    $query->condition('a.id', $id, '=');
    $result = $query->execute()->fetchObject();
    return $result;
  }

  /**
   * To insert a new author record.
   *
   * @param array $fields
   *   An array conating the author data in key value pair.
   */
  public static function add(array $fields) {
    return \Drupal::database()->insert('bn_author')->fields($fields)->execute();
  }

  /**
   * To update an existing employee record.
   *
   * @param int $id
   *   The author ID.
   * @param array $fields
   *   An array conating the author data in key value pair.
   */
  public static function update($id, array $fields) {
    return \Drupal::database()->update('bn_author')->fields($fields)
      ->condition('id', $id)
      ->execute();
  }

  /**
   * To delete a specific author record.
   *
   * @param int $id
   *   The author ID.
   */
  public static function delete($id) {
    $record = self::load($id);
    if ($record->getPicture()) {
      file_delete($record->picture);
    }
    return \Drupal::database()->delete('bn_author')->condition('id', $id)->execute();
  }

  /**
   * To activate/ block the author record.
   *
   * @param int $id
   *   The author ID.
   * @param int $status
   *   Set 1 for activatng and 0 for blocking.
   */
  public static function changeStatus($id, $status) {
    return self::update($id, ['status' => ($status) ? 1 : 0]);
  }

  /**
   * Get the list of Authors in the select format
   */
  public static function getAuthorsSelectFormat($newOption = false, $emptyOption = 'Seleccione una opcion') {
    $query = \Drupal::database()->select('bn_author', 'a')
      ->fields('a', ['id', 'first_name', 'last_name']);
    // Query execution.
    $result = $query->execute()->fetchAll();

    $select_options = [];
    //$select_options[0] = 'Seleccione un Autor';
    if($newOption)
      $select_options[-1] = 'Crear nuevo Autor';
    foreach($result as $key => $row) {
      $select_options[$row->id] = $row->first_name.', '.$row->last_name;
    }

    return $select_options;
  }

  /**
   * Get number of instance from a determined item
   * @param int $itemId
   *
   * @return $authorList
   */
  public static function getAuthorsFromItem($itemId) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', 'first_name', 'last_name', 'picture', 'nationality', 'description', 'status', 'createdon', 'updatedon']);

    // Add join to bn_countries table
    $query->leftjoin('bn_countries', 'c', 'c.id = a.nationality');
    $query->fields('c', ['id', 'en_short_name', 'nationality']);

    // Add join to bn_instance table
    $query->join('bn_item_author', 'ia', 'ia.author_id = ' . self::TABLE_ALIAS . '.id');
    // Add join to bn_instance_status table
    $query->join('bn_item', 'i', 'i.id = ia.item_id');
    $query->condition('i.id', $itemId, '=');

    $result = $query->execute()->fetchAll();

    //Now we have to build the DTO list result.
    $resultsDTO = [];
    // DB results iterations
    foreach ($result as $key => $row) {
      $entityDTO = AuthorDAO::getAuthorDTOFromRecord($row);
      // Add element to result array
      $resultsDTO[$entityDTO->getId()] = $entityDTO;
    }
    return $resultsDTO;
  }

  /** Utis methods *********************************************************************************/
  /**
   * Create an AuthorDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return EquipoTipoDTO $authorDTO
   *   DTO object
   */
  private static function getAuthorDTOFromRecord($row) {
    $authorDTO = new AuthorDTO();
    $createdBy = new UserDTO();
    $updatedBy = new UserDTO();

    // set simple fields
    $authorDTO->setId($row->id);
    $authorDTO->setfirstName($row->first_name);
    $authorDTO->setLastName($row->last_name);
    $authorDTO->setPicture($row->picture);
    $authorDTO->setDescription($row->description);

    if(isset($row->nationality)) {
      $nationalityDTO = new NationalityDTO();
      $nationalityDTO->setId($row->c_id);
      $nationalityDTO->setNationality($row->c_nationality);
      $nationalityDTO->setEnShortName($row->en_short_name);
      $authorDTO->setNationality($nationalityDTO);
    }

    $authorDTO->setStatus($row->status);

    // set audit fields
    $createdBy->setUid($row->createdby_uid);
    $createdBy->setUsername($row->createdby);
    $authorDTO->setCreatedBy($createdBy);

    $authorDTO->setCreatedOn($row->createdon);

    $updatedBy->setUid($row->updatedby_uid);
    $updatedBy->setUsername($row->updatedby);
    $authorDTO->setUpdatedBy($updatedBy);

    $authorDTO->setUpdatedOn($row->updatedon);

    return $authorDTO;
  }
}
