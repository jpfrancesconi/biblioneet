<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DAOs\GenericDAO;

use Drupal\io_generic_abml\DTOs\BookDTO;
use Drupal\io_generic_abml\DTOs\ArticleDTO;
use Drupal\io_generic_abml\DTOs\ArticleTypeDTO;
use Drupal\io_generic_abml\DTOs\ArticleFormatDTO;
use Drupal\io_generic_abml\DTOs\EditorialDTO;
use Drupal\io_generic_abml\DTOs\UserDTO;


/**
 * DAO class for article entity.
 */
class ArticleDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'bn_article';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'art';

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
    if (!isset($limit))
      $limit = 15;
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, [
        'id', 'title', 'cover', 'inv_code', 'createdon', 'updatedon'
      ]);

    // Add join to bn_article_type table
    $query->leftjoin('bn_article_type', 'at', 'at.id = '.self::TABLE_ALIAS.'.article_type_id');
    $query->fields('at', ['id', 'type', 'status']);

    // Add join to bn_article_format table
    $query->leftjoin('bn_article_format', 'af', 'af.id = '.self::TABLE_ALIAS.'.article_format_id');
    $query->fields('af', ['id', 'format', 'status']);

    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);
    // If $search_key is not null means that need to add the where condition.
    if (!is_null($search_key)) {
      $query->condition(self::TABLE_ALIAS . '.title', "%" . Html::escape($search_key) . "%", 'LIKE');
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
      $entityDTO = self::getArticleDTOFromRecord($row);
      // Add element to result array
      array_push($resultsDTO, $entityDTO);
    }
    // Add the DTOs to the results array.
    $results['resultsDTO'] = $resultsDTO;
    return $results;
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
  private static function getArticleDTOFromRecord($row) {
    // Check Article Format to create the correct Object
    if (isset($row->af_id)) {
      switch ($row->type) {
        case 'LIBRO':
          $articleDTO = BookDAO::loadByArticleId($row->id);
          break;
        case 'REVISTA':
          $articleDTO = MagazineDAO::loadByArticleId($row->id);
          break;
        case 'MULTIMEDIA':
          # code...
          break;
        case 'MONOGRAFÍA':
          # code...
          break;
        case 'DIARIO/PERIÓDICO':
          # code...
          break;  

        default:
          # code...
          break;
      }
    }
    
    //$articleDTO = new ArticleDTO();
    $createdBy = new UserDTO();
    $updatedBy = new UserDTO();

    // set simple fields
    $articleDTO->setId($row->id);
    $articleDTO->setTitle($row->title);
    $articleDTO->setCover($row->cover);
    $articleDTO->setInvCode($row->inv_code);
    
    // Article Type
    if (isset($row->at_id)) {
       $articleTypeDTO = new ArticleTypeDTO();
       $articleTypeDTO->setId($row->at_id);
       $articleTypeDTO->setType($row->type);
       $articleTypeDTO->setStatus($row->status);
       $articleDTO->setArticleType($articleTypeDTO);
    }

    // Article Format
    if (isset($row->af_id)) {
      $articleFormatDTO = new ArticleFormatDTO();
      $articleFormatDTO->setId($row->af_id);
      $articleFormatDTO->setFormat($row->format);
      $articleFormatDTO->setStatus($row->af_status);
      $articleDTO->setArticleFormat($articleFormatDTO);
   }

    // set audit fields
    $createdBy->setUid($row->createdby_uid);
    $createdBy->setUsername($row->createdby);
    $articleDTO->setCreatedBy($createdBy);

    $articleDTO->setCreatedOn($row->createdon);

    $updatedBy->setUid($row->updatedby_uid);
    $updatedBy->setUsername($row->updatedby);
    $articleDTO->setUpdatedBy($updatedBy);

    $articleDTO->setUpdatedOn($row->updatedon);

    return $articleDTO;
  }
}
