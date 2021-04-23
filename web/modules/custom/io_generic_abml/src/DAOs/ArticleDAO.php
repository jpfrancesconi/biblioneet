<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DAOs\GenericDAO;
use Drupal\io_generic_abml\DAOs\InstanceDAO;

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
  public static function getAll($search_article = NULL, $search_article_type = NULL, $limit = NULL) {
    if (!isset($limit))
      $limit = 10;
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

    // Now we have to check if user has selected any filter: $search_article_type
    if(isset($search_article_type)) {
      switch ($search_article_type) {
        case '0':
          # TODOS
          // If $search_key is not null means that need to add the where condition.
          if (!is_null($search_article)) {
            // Add LEFT JOIN to bn_article_author table
            $query->leftjoin('bn_article_author', 'art_aut', 'art_aut.article_id = ' . self::TABLE_ALIAS . '.id');
            // Add LEFT JOIN to bn_book table
            $query->leftjoin('bn_book', 'bk', 'bk.article_id = ' . self::TABLE_ALIAS . '.id');
            // Add LEFT JOIN to bn_author table
            $query->leftjoin('bn_author', 'aut', 'aut.id = art_aut.author_id');
            $group = $query->orConditionGroup()
              ->condition(self::TABLE_ALIAS . '.title', "%" . Html::escape($search_article) . "%", 'LIKE')
              ->condition('aut.first_name', "%" . Html::escape($search_article) . "%", 'LIKE')
              ->condition('aut.last_name', "%" . Html::escape($search_article) . "%", 'LIKE')
              ->condition('bk.isbn', "%" . Html::escape($search_article) . "%", 'LIKE');
            $query->condition($group);
          }
          break;
        case '1':
          # TITULO
          // If $search_key is not null means that need to add the where condition.
          if (!is_null($search_article)) {
            $query->condition(self::TABLE_ALIAS . '.title', "%" . Html::escape($search_article) . "%", 'LIKE');
          }
          break;

        case '2':
          # AUTOR
          if (!is_null($search_article)) {
            // Add LEFT JOIN to bn_article_author table
            $query->leftjoin('bn_article_author', 'art_aut', 'art_aut.article_id = ' . self::TABLE_ALIAS . '.id');
            // Add LEFT JOIN to bn_author table
            $query->leftjoin('bn_author', 'aut', 'aut.id = art_aut.author_id');
            $group = $query->orConditionGroup()
            ->condition('aut.first_name', "%" . Html::escape($search_article) . "%", 'LIKE')
            ->condition('aut.last_name', "%" . Html::escape($search_article) . "%", 'LIKE');
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
          $query->condition('bk.isbn', "%" . Html::escape($search_article) . "%", 'LIKE');
          break;

        default:
          # code...
          $a =1;
          break;
      }
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

  /**
   * To load an Article record.
   *
   * @param int $id
   *   The article ID.
   */
  public static function load($id) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, [
        'id', 'title', 'cover', 'inv_code', 'createdon', 'updatedon'
      ]);

    // Add join to bn_article_type table
    $query->leftjoin('bn_article_type', 'at', 'at.id = ' . self::TABLE_ALIAS . '.article_type_id');
    $query->fields('at', ['id', 'type', 'status']);

    // Add join to bn_article_format table
    $query->leftjoin('bn_article_format', 'af', 'af.id = ' . self::TABLE_ALIAS . '.article_format_id');
    $query->fields('af', ['id', 'format', 'status']);

    // Get crator username
    $query = parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition(self::TABLE_ALIAS . '.id', $id, '=')->execute()->fetchObject();
    $articleDTO = self::getArticleDTOFromRecord($result);

    return $articleDTO;
  }

  /**
   * To insert a new record into DB.
   *
   * @param array $fields
   *   An array conating the author data in key value pair.
   */
  public static function add(array $fields, $instances = NULL) {
    $idNewArticle = \Drupal::database()->insert(self::TABLE_NAME)->fields($fields)->execute();

    if(isset($instances)){
      for ($i=0; $i < $instances; $i++) {
        $fields2 = [
          'instance_status_id' => 1, //DISPONIBLE
          'article_id' => $idNewArticle,
          'createdby' => $fields['createdby'],
        ];
        InstanceDAO::add($fields2);
      }
    }

    return $idNewArticle;
  }

  /**
   * Get the list of Article Types in the select format
   */
  public static function getArticlesTypesSelectFormat($status = NULL, $opcion_vacia) {
    $select_options = parent::getListSelectFormat('bn_article_type', 'type', $status, $opcion_vacia);

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
  public static function getEditorialesSelectFormat($status = NULL, $opcion_vacia) {
    $select_options = parent::getListSelectFormat('bn_editorial', 'editorial', $status, $opcion_vacia);

    return $select_options;
  }
  /** Utis methods *********************************************************************************/
  /**
   * Create a ArticleDTO from stdClass from DB Record
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
          $articleDTO = MultimediaDAO::loadByArticleId($row->id);
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
