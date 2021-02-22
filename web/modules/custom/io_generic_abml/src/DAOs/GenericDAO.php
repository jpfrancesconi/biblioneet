<?php
namespace Drupal\io_generic_abml\DAOs;

use \Drupal\Core\Database\Query\SelectInterface;

class GenericDAO {
  /**
   * Add the audit fields to the query
   * @param SelectInterface $query
   * @param string $table_alias
   * @return SelectInterface
   */
  protected static function addAuditFields(SelectInterface $query, string $table_alias) {
    // Get crator username
    $query->addField('usualt', 'uid', 'createdby_uid');
    $query->addField('usualt', 'name', 'createdby');
    // Get updater username
    $query->addField('usumod', 'uid', 'updatedby_uid');
    $query->addField('usumod', 'name', 'updatedby');
    // Join with user table
    $query->join('users_field_data', 'usualt', 'usualt.uid = ' . $table_alias .'.createdby');
    $query->leftJoin('users_field_data', 'usumod', 'usumod.uid = ' . $table_alias .'.updatedby');

    return $query;
  }

  /**
   * Add to the  array of results the start and end records number.
   *
   * @param SelectInterface $query
   * @param array $results
   * @param int $limit
   * @return array
   */
  protected static function addSummaryCounterData(SelectInterface $query, $results, $limit) {
    $request = \Drupal::requestStack()->getCurrentRequest()->query->all();
    $page = 0;
    if (isset($request['page'])) {
      $page = $request['page'];
    }

    $queryCounter = $query->getCountQuery();
    $counter = $queryCounter->execute()->fetchObject();
    $rowsCount = $counter->expression;

    $start = ($limit * $page) + 1;
    $end = $start + ($rowsCount - 1);

    $results['counter']['start'] = $start;
    $results['counter']['end'] = $end;

    return $results;
  }
  /**
   * Add to the  array of results the total records number.
   *
   * @param SelectInterface $query
   * @param array $results
   * @return array
   */
  protected static function addTotalCounterData(SelectInterface $query, $results) {
    $queryCounter = $query->getCountQuery();
    $counter = $queryCounter->execute()->fetchObject();
    $results['counter']['total'] = $counter->expression;

    return $results;
  }

  /**
   * Get the list of record of a table
   *
   * @param string $table_name
   *  Nombre de la tabla a la cual se quiere consultar
   * @param bool $activo
   *  TRUE if needs only the active records
   *  FALSE if need only the non active records
   *  NULL if need all records
   */
  public static function getList($table_name, $activo = NULL) {
    $query = \Drupal::database()->select($table_name, $table_name)
      ->fields($table_name);
      // Add the audit fields to the query.
    $query =  self::addAuditFields($query, $table_name);
    // If $activo is not null, add the condition to filter by activo
    if(!is_null($activo)) {
      $query->condition($table_name. '.status', $activo, '=');
    }

    // Query execution.
    $result = $query->execute()->fetchAll();

    return $result;
  }

  /**
   * Get the list of records of a table in an array of type id => description
   *
   * @param string $table_name
   *  Table of the reuqired table
   * @param string $description_field
   *  Table field name used as a description field on the results array
   * @param bool $activo
   *  TRUE if needs only the active records
   *  FALSE if need only the non active records
   *  NULL if need all records
   * @return array
   */
  public static function getListSelectFormat($table_name, $description_field, $activo = NULL) {
    $result = self::getList($table_name, $activo);
    $select_options = [];
    foreach($result as $key => $row) {
      $select_options[$row->id] = $row->{$description_field};
    }

    return $select_options;
  }
}
