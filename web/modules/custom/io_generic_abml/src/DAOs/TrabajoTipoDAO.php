<?php
namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DTOs\TrabajoTipoDTO;
use Drupal\io_generic_abml\DTOs\UsuarioDTO;
use Drupal\io_generic_abml\DAOs\GenericDAO;

class TrabajoTipoDAO extends GenericDAO {
  /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'io_trabajo_tipo';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'tt';
  /**
   * @var const TABLE_FIELD
   *  Name of the db table field used when the user send a search
   */
  private const TABLE_FIELD = 'tipo';

  /**
   * To get multiple Tipos de clasificación de eactividad records.
   *
   * @param array $header
   *   The table header used to sort the results
   * @param string $search_key
   *   The search string to filter
   * @param int $limit
   *   The number of records to be fetched.
   */
  public static function getAll($header, $search_key = NULL ,$limit = NULL) {
    $limit = 15;
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', self::TABLE_FIELD, 'activo', 'fecha_alta', 'fecha_mod']);
    // Add the audit fields to the query.
    $query =  parent::addAuditFields($query, self::TABLE_ALIAS);
    // If $search_key is not null means that need to add the where condition.
    if (!is_null($search_key)) {
      $query->condition(self::TABLE_ALIAS. '.' .self::TABLE_FIELD, "%" . Html::escape($search_key) . "%", 'LIKE');
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
      $entityDTO = self::getTrabajoTipoDTOFromRecord($row);
      // Add element to result array
      array_push($resultsDTO, $entityDTO);
    }
    // Add the DTOs to the results array.
    $results['resultsDTO'] = $resultsDTO;
    return $results;
  }

  /**
   * Load a Prioridad entitie.
   *
   * @param int $id
   *  The Prioridad ID
   */
  public static function load($id) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
    ->fields(self::TABLE_ALIAS, ['id', self::TABLE_FIELD, 'activo', 'fecha_alta', 'fecha_mod']);

    $query = parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition('id', $id, '=')->execute()->fetchObject();
    $entityDTO = self::getTrabajoTipoDTOFromRecord($result);

    return $entityDTO;
  }

  /**
   * To check if a tipo de trabajo is valid.
   *
   * @param int $id
   *   The tipo de trabajo ID.
   */
  public static function exists($id) {
    $result = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id'])
      ->condition('id', $id, '=')
      ->execute()
      ->fetchField();
    return (bool) $result;
  }

  /**
   * Edit an existing tipo de trabajo
   *
   * @param int $id
   *   The tipo de trabajo ID.
   * @param array $fields
   *   An array with the entity data in key value pair.
   * @return int
   *  Id of the tipo de trabajo added.
   */
  public static function update($id, array $fields) {
    $id = \Drupal::database()->update(self::TABLE_NAME)
    ->fields($fields)
    ->condition('id', $id, '=')
    ->execute();

    return $id;
  }

  /**
   * To delete a specific tipo de trabajo record.
   *
   * @param int $id
   *   The tipo de trabajo ID.
   */
  public static function delete($id) {
    if (self::exists($id)) {
      return \Drupal::database()
      ->delete(self::TABLE_NAME)
      ->condition('id', $id)
      ->execute();
    }
    return;
  }

  /**
   * Add a new tipo de trabajo
   *
   * @param array $fields
   *   An array with the entity data in key value pair.
   * @return int
   *  Id of the tipo de trabajo added.
   */
  public static function add(array $fields) {
    $id = \Drupal::database()->insert(self::TABLE_NAME)
    ->fields($fields)
    ->execute();

    return $id;
  }

  /**
   * To check if a  tipo de trabajo name exists in the db.
   *
   * @param string $name
   *   The tipo de trabajo name.
   */
  public static function repeated($name) {
    $result = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id'])
      ->condition(self::TABLE_FIELD, $name, '=')
      ->execute()
      ->fetchField();
    return (bool) $result;
  }

   /**
   * Create an TrabajoTipoDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return TrabajoTipoDTO $trabajoTipoDTO
   *   DTO object
   */
  private static function getTrabajoTipoDTOFromRecord($row) {
    $trabajoTipoDTO = new TrabajoTipoDTO();
    $usuAltaDTO = new UsuarioDTO();
    $usuModDTO = new UsuarioDTO();

    $trabajoTipoDTO->setId($row->id);
    $trabajoTipoDTO->setTipoTrabajo($row->tipo);
    $trabajoTipoDTO->setActivo($row->activo);

    $usuAltaDTO->setUid($row->usuario_alta_uid);
    $usuAltaDTO->setUsername($row->usuario_alta);
    $trabajoTipoDTO->setUsuarioAlta($usuAltaDTO);

    $trabajoTipoDTO->setFechaAlta($row->fecha_alta);

    $usuModDTO->setUid($row->usuario_mod_uid);
    $usuModDTO->setUsername($row->usuario_mod);
    $trabajoTipoDTO->setUsuarioMod($usuModDTO);

    $trabajoTipoDTO->setFechaMod($row->fecha_mod);

    return $trabajoTipoDTO;
  }
}

