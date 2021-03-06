<?php

namespace CINC\Project\Importer\GoogleSheets\ConfigType;

/**
 * Provides Google Sheets import functionality for bean types.
 *
 * @author Scott Reynen <scott@slicedbreadlabs.com>
 */
class BeanType {

  function sheetsToLists($importer) {

    $result = array();
    $columns = array();

    // Get the right worksheet.
    $worksheet = $importer->worksheetFromSheets(
      $importer->sheets,
      array('bean types')
    );
    if (!$worksheet) {
      return FALSE;
    }

    // Map header names to column numbers.
    $header = array_shift($worksheet);
    foreach ($header as $index => $name) {
      $columns[strtolower($name)] = $index;
    }

    // Reorganize rows by header names.
    foreach ($worksheet as $row) {
      $resultRow = array();
      foreach ($columns as $name => $index) {
        if (isset($row[$index])) {
          $resultRow[$name] = $row[$index];
        }
      }
      if (
        (isset($resultRow['machine name'])) &&
        (!empty($resultRow['machine name']))
      ) {
        $result[] = $resultRow;
      }
    }

    $importer->lists['bean_type'] = $result;
  }

  function listsToYaml($importer, $project) {
    if (isset($importer->lists['bean_type'])) {
      foreach ($importer->lists['bean_type'] as $beanType) {
        $type = array(
          'label' => $beanType['name'],
          'name' => $beanType['machine name'],
          'description' => isset($beanType['description']) ?
            $beanType['description'] : '',
        );
        $name = 'bean_type.' . $beanType['machine name'] . '.yml';
        $project->yaml[$name] = $importer->yamlDumper->dump($type, 9999);
      }
    }
  }

}
