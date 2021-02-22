<?php

namespace Drupal\io_generic_abml\DAOs;

use Drupal\Component\Utility\Html;

use Drupal\io_generic_abml\DTOs\UsuarioDTO;
use Drupal\io_generic_abml\DTOs\EquipoTipoDTO;
use Drupal\io_generic_abml\DTOs\CampoPersonalizadoDTO;
use Drupal\io_generic_abml\DTOs\CampoPersonalizadoTipoDTO;

use Drupal\io_generic_abml\DAOs\GenericDAO;

/**
 * DAO class for Tipos de Equipos table.
 */
class EquipoTiposDAO extends GenericDAO {
    /**
   * @var const TABLE_NAME
   *  Name of the db table related with the entity
   */
  private const TABLE_NAME = 'io_equipo_tipo';
  /**
   * @var const TABLE_ALIAS
   *  Table alias used by the differents querys
   */
  private const TABLE_ALIAS = 'et';
  /**
   * @var const TABLE_FIELD
   *  Name of the db table field used when the user send a search
   */
  private const TABLE_FIELD = 'tipo';
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
      $entityDTO = self::getEquipoTipoDTOFromRecord($row);
      // Add element to result array
      array_push($resultsDTO, $entityDTO);
    }
    // Add the DTOs to the results array.
    $results['resultsDTO'] = $resultsDTO;
    return $results;
  }

  /**
   * To load an equipo_tipo record.
   *
   * @param int $id
   *   The author ID.
   */
  public static function load($id) {
    $query = \Drupal::database()->select(self::TABLE_NAME, self::TABLE_ALIAS)
      ->fields(self::TABLE_ALIAS, ['id', self::TABLE_FIELD, 'activo', 'fecha_alta', 'fecha_mod']);
    // Get crator username
    $query = parent::addAuditFields($query, self::TABLE_ALIAS);

    $result = $query->condition('id', $id, '=')->execute()->fetchObject();
    $equipoTipoDTO = self::getEquipoTipoDTOFromRecord($result);

    return $equipoTipoDTO;
  }

  /**
   * To load a list of Campos Personalizados of a Equipo Tipo given ID.
   *
   * @param int $id
   *   The equipo_tipo ID.
   *
   * @return CampoPersonalizadoDTO $listDTO
   *   The io_campo_personalizado list.  
   */
  public static function getCamposPersonalizadosbyEquipoTipoId($equipoTipoId) {
    // SELECT 
    //   cp.etiqueta, cp.equipo_tipo_id, cp.campo_personalizado_tipo_id,
    //     cpt.tipo_campo
    // FROM io_campo_personalizado cp
    // JOIN io_equipo_tipo et ON et.id = cp.equipo_tipo_id
    // JOIN io_campo_personalizado_tipo cpt ON cpt.id = cp.campo_personalizado_tipo_id
    // WHERE et.id = 1;

    $query = \Drupal::database()->select('io_campo_personalizado', 'cp')
      ->fields('cp', ['id', 'etiqueta', 'equipo_tipo_id', 'campo_personalizado_tipo_id', 'fecha_alta', 'fecha_mod']);
    // Get crator username
    $query = parent::addAuditFields($query, 'cp');

    // Get equipo tipo relacionado
    $query->addField('et', 'id', 'equipo_tipo_id');
    $query->addField('et', 'tipo', 'equipo_tipo');
    // Join with equipo_tipo table
    $query->join('io_equipo_tipo', 'et', 'et.id = cp.equipo_tipo_id');

    // Get campo personalizado tipo relacionados
    $query->addField('cpt', 'tipo_campo', 'tipo_campo');
    // Join with equipo_tipo table
    $query->join('io_campo_personalizado_tipo', 'cpt', 'cpt.id = cp.campo_personalizado_tipo_id');

    $result = $query->condition('equipo_tipo_id', $equipoTipoId, '=')->execute()->fetchAll();
    

    //Now we have to build the DTO list result.
    $resultsDTO = [];
    // DB results iterations
    foreach ($result as $key => $row) {
      $entityDTO = self::getCampoPersonalizadoDTOFromRecord($row);
      // Add element to result array
      array_push($resultsDTO, $entityDTO);
    }
    return $resultsDTO;
  }

  /**
   * To load a list of Campos Personalizados of a Equipo Tipo given ID.
   *
   * @param int $id
   *   The equipo_tipo ID.
   *
   * @return CampoPersonalizadoDTO $listDTO
   *   The io_campo_personalizado list.  
   */
  public static function getOpcionesByCampoPersonalizadoId($campoPersonalizadoId) { 
    $query = \Drupal::database()->select('io_campo_personalizado_opciones', 'cpo')
      ->fields('cpo', ['id', 'opcion', 'campo_personalizado_id', 'fecha_alta', 'fecha_mod']);
    // Get crator username
    $query = parent::addAuditFields($query, 'cpo');

    $result = $query->condition('campo_personalizado_id', $campoPersonalizadoId, '=')->execute()->fetchAll();

    //Now we have to build the DTO list result.
    $results = [];
    // DB results iterations
    foreach ($result as $key => $row) {
      $results[$row->id] = $row->opcion;
    }
    return $results;
  }


  /**
   * To check if an tipo de equipo is valid.
   *
   * @param int $id
   *   The equipo_tipo ID.
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
   * To update an existing equipo_tipo record.
   *
   * @param int $id
   *   The equipo_tipo ID.
   * @param array $fields
   *   An array with the entity data in key value pair.
   */
  public static function update($id, array $fields) {
    return \Drupal::database()->update(self::TABLE_NAME)
      ->fields($fields)
      ->condition('id', $id)
      ->execute();
  }

  /**
   * To delete a specific equipo_tipo record.
   *
   * @param int $id
   *   The equipo_tipo ID.
   */
  public static function delete($id) {
    if (self::exists($id)) {
      return \Drupal::database()->delete(self::TABLE_NAME)
        ->condition('id', $id)
        ->execute();
    }
    return;
  }

  /**
   * To insert a new equipo_tipo record.
   *
   * @param array $fields
   *   An array with the equipo_tipo data in key value pair.
   */
  public static function add(array $fields) {
    return \Drupal::database()->insert(self::TABLE_NAME)
      ->fields($fields)
      ->execute();
  }

  /**
   * To check if n Equipo tipo name exists in the db.
   *
   * @param string $name
   *   The equipo_tipo Name.
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
   * Get the list of equipo tipo in the select format
   */
  public static function getEquipoTipoSelectFormat() {
    $select_options = parent::getListSelectFormat(self::TABLE_NAME, 'tipo', TRUE);

    return $select_options;
  }

  /**
   * Create an EquipoTipoDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return EquipoTipoDTO $equipoTipoDTO
   *   DTO object
   */
  private static function getEquipoTipoDTOFromRecord($row) {
    $equipoTipoDTO = new EquipoTipoDTO();
    $usuAltaDTO = new UsuarioDTO();
    $usuModDTO = new UsuarioDTO();

    $equipoTipoDTO->setId($row->id);
    $equipoTipoDTO->setTipo($row->tipo);
    $equipoTipoDTO->setActivo($row->activo);

    $usuAltaDTO->setUid($row->usuario_alta_uid);
    $usuAltaDTO->setUsername($row->usuario_alta);
    $equipoTipoDTO->setUsuarioAlta($usuAltaDTO);

    $equipoTipoDTO->setFechaAlta($row->fecha_alta);

    $usuModDTO->setUid($row->usuario_mod_uid);
    $usuModDTO->setUsername($row->usuario_mod);
    $equipoTipoDTO->setUsuarioMod($usuModDTO);

    $equipoTipoDTO->setFechaMod($row->fecha_mod);

    return $equipoTipoDTO;
  }

  /**
   * Create an CampoPersonalizadoDTO from stdClass from DB Record
   *
   * @param stdClass $row
   *   stdClass DB record
   * @return CampoPersonalizadoDTO $campoPersonalizadoDTO
   *   DTO object
   */
  private static function getCampoPersonalizadoDTOFromRecord($row) {
    $campoPersonalizadoDTO = new CampoPersonalizadoDTO();
    $usuAltaDTO = new UsuarioDTO();
    $usuModDTO = new UsuarioDTO();

    $campoPersonalizadoDTO->setId($row->id);
    $campoPersonalizadoDTO->setEtiqueta($row->etiqueta);
    if(isset($row->campo_personalizado_tipo_id)) {
      $campoPersonalizadoTipoDTO = new CampoPersonalizadoTipoDTO();
      $campoPersonalizadoTipoDTO->setId($row->campo_personalizado_tipo_id);
      $campoPersonalizadoTipoDTO->setTipoCampo($row->tipo_campo);
      $campoPersonalizadoDTO->setCampoPersonalizadoTipo($campoPersonalizadoTipoDTO);
    }
    
    $usuAltaDTO->setUid($row->usuario_alta_uid);
    $usuAltaDTO->setUsername($row->usuario_alta);
    $campoPersonalizadoDTO->setUsuarioAlta($usuAltaDTO);

    $campoPersonalizadoDTO->setFechaAlta($row->fecha_alta);

    $usuModDTO->setUid($row->usuario_mod_uid);
    $usuModDTO->setUsername($row->usuario_mod);
    $campoPersonalizadoDTO->setUsuarioMod($usuModDTO);

    $campoPersonalizadoDTO->setFechaMod($row->fecha_mod);

    return $campoPersonalizadoDTO;
  }
}
