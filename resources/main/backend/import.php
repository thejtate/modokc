<?php

/**
 * @file Import emails from xlsx to serialized json.
 */

//disabled
die();

define('SOURCE_FILE_PATH', 'emails_source/HouseMemberswithZipCode.xlsx');
define('EMAILS_LIST_FILE_PATH', 'emails_list.json');
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** PHPExcel_IOFactory */
require_once dirname(__FILE__) . '/libraries/PHPExcel/Classes/PHPExcel/IOFactory.php';


if (!file_exists(SOURCE_FILE_PATH)) {
  exit('File not exist ' . SOURCE_FILE_PATH . EOL);
}

//test code
//echo '<pre>';
//print_r(get_reciever_emails_by_zip(74881));
//echo '</pre>';
//die();
//test code



$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(SOURCE_FILE_PATH);

$export_array = array();


foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {


  foreach ($worksheet->getRowIterator(3) as $row) {


    /**
     * @var PHPExcel_Worksheet_Row $row
     */

    /**
     * @var PHPExcel_Worksheet_CellIterator $cellIterator
     */
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(FALSE); // Loop all cells, even if it is not set

    $cell = $cellIterator->current();
    $name = !is_null($cell) ? $cell->getValue() : '';
    $cellIterator->next();

    $cell = $cellIterator->current();
    $email = !is_null($cell) ? $cell->getValue() : '';
    $cellIterator->next();
    $cellIterator->next();
    $cellIterator->next();
    $cellIterator->next();
    $cellIterator->next();
    $cellIterator->next();


    if (!is_null($cell)) {
      $cell = $cellIterator->current();
      $zip_list = !is_null($cell) ? explode(',', $cell->getCalculatedValue()) : array();
      array_walk($zip_list, function(&$v) {$v = trim($v);});
      foreach ($zip_list as $zip) {
        $export_array[$zip][] = array('name' => $name, 'email' => $email);
      }
    }

//    foreach ($cellIterator as $cell) {
//      if (!is_null($cell)) {
//        echo '        Cell - ' , $cell->getColumn() , ' - ' , $cell->getCalculatedValue();
//      }
//    }
  }

  file_put_contents(EMAILS_LIST_FILE_PATH, json_encode($export_array));
  echo 'Saved to: ' . EMAILS_LIST_FILE_PATH;
  echo '<pre>';
  print_r($export_array);
  echo '</pre>';



}

function get_reciever_emails_by_zip($zip) {

  $zip_emails_list = file_get_contents(EMAILS_LIST_FILE_PATH);

  echo '<pre>';
  print_r($zip_emails_list);
  echo '</pre>';

  $zip_emails_list = json_decode($zip_emails_list, true);

  return isset($zip_emails_list[$zip]) ? $zip_emails_list[$zip] : FALSE;
}