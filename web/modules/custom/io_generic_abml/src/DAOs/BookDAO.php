<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DAOs\GenericDAO;

use Drupal\io_generic_abml\DTOs\BookDTO;
use Drupal\io_generic_abml\DTOs\EditorialDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;


/**
 * DAO class for book entity.
 */
class BookDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'bn_book';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'b';

  /**
   * To get multiple bn_book records.
   *
   * @param array $header
   *   The table header used to sort the results
   * @param string $search_key
   *   The search string to filter
   * @param int $limit
   *   The number of records to be fetched.
   */
  public static function getAll($search_key = NULL, $limit = NULL) {
    if(!isset($limit))
      $limit = 15;
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', 'isbn', 'titulo'
            , 'anio_edicion', 'cant_paginas', 'idioma', 'createdon', 'updatedon']);

    // Add join to bn_countries table
    $query->leftjoin('bn_editorial', 'ed', 'ed.id = b.editorial_id');
    $query->fields('ed', ['id', 'editorial', 'status']);

    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);
    // If $search_key is not null means that need to add the where condition.
    if (!is_null($search_key)) {
      $query->condition(self::TABLE_ALIAS . '.titulo', "%" . Html::escape($search_key) . "%", 'LIKE');
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
      $entityDTO = self::getBookDTOFromRecord($row);
      // Add element to result array
      array_push($resultsDTO, $entityDTO);
    }
    // Add the DTOs to the results array.
    $results['resultsDTO'] = $resultsDTO;
    return $results;
  }

  /**
   * To insert a new record into DB.
   *
   * @param array $fields
   *   An array conating the book data in key value pair.
   */
  public static function add(array $fields) {
    return \Drupal::database()->insert(self::TABLE_NAME)->fields($fields)->execute();
  }

  /**
   * To load a bn_book record.
   *
   * @param int $id
   *   The article ID.
   */
  public static function loadByArticleId($articleId) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', 'isbn', 'anio_edicion', 'cant_paginas', 'idioma', 'createdon', 'updatedon']);

    // Add join to bn_countries table
    $query->leftjoin('bn_editorial', 'ed', 'ed.id = b.editorial_id');
    $query->fields('ed', ['id', 'editorial', 'status']);

      // Get crator username
    $query = parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition('article_id', $articleId, '=')->execute()->fetchObject();

    $bookDTO = self::getBookDTOFromRecord($result);

    return $bookDTO;
  }

  /** Utis methods *********************************************************************************/
  /**
   * Create a BookDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return BookDTO $bookDTO
   *   DTO object
   */
  private static function getBookDTOFromRecord($row) {
    $bookDTO = new BookDTO();
    $createdBy = new UserDTO();
    $updatedBy = new UserDTO();

    // set simple fields
    $bookDTO->setIdBook($row->id);
    $bookDTO->setIsbn($row->isbn);
    $bookDTO->setAnioEdicion($row->anio_edicion);
    $bookDTO->setCantPaginas($row->cant_paginas);
    $bookDTO->setIdioma($row->idioma);

    if (isset($row->ed_id)) {
      $editorialDTO = new EditorialDTO();
      $editorialDTO->setId($row->ed_id);
      $editorialDTO->setEditorial($row->editorial);
      $editorialDTO->setActivo($row->status);
      $bookDTO->setEditorial($editorialDTO);
    }

    // set audit fields
    $createdBy->setUid($row->createdby_uid);
    $createdBy->setUsername($row->createdby);
    $bookDTO->setCreatedBy($createdBy);

    $bookDTO->setCreatedOn($row->createdon);

    $updatedBy->setUid($row->updatedby_uid);
    $updatedBy->setUsername($row->updatedby);
    $bookDTO->setUpdatedBy($updatedBy);

    $bookDTO->setUpdatedOn($row->updatedon);

    return $bookDTO;
  }

}
