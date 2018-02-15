<?php
/**
 * @file processing log
 */

define("PATH_TO_MAIL_LOG_FILE", __DIR__ . "/../logs/");



function getMailLogMessage($fields){
  $message = '';

  foreach ($fields as $key => $field) {
    if(!empty($field['type']) && $field['type'] == 'checkboxes') { //checkboxes field
      $all_chexboxes_empty = TRUE;
      foreach ($field['names'] as $checkobox_name) {
        if(!empty($_POST[$checkobox_name])) {
          $all_chexboxes_empty = FALSE;
          break;//exit foreach
        }
      }
      if(!$all_chexboxes_empty) {
        $message .=  $field["label"] . ":  \r\n";
        foreach ($field['names'] as $checkobox_name) {
          $message .= !empty($_POST[$checkobox_name]) ? htmlspecialchars($_POST[$checkobox_name]) .  "\r\n" : "";
        }
      }

    } else { // not checkboxes
      $message .= $field['label'] . ': ' . htmlspecialchars(isset($_POST[$key]) ? $_POST[$key] : '') .  "\r\n";
    }
  }

  return $message;
}


function prepareMailLog($fields, $log_file_path) {
  $log_string = getMailLogMessage($fields);
  writeToLog($log_string, $log_file_path);
}


/**
 * Write log string or array to log file
 * @param string $string
 */
function writeToLog($string = "", $log_file_path) {
  try {
    if (is_array($string)) {
      $string = var_export($string, TRUE);
    }

    $string = "\r\n\r\n" . "===============================================" . "\r\n" .
      "Date: " . date("m/d/Y h:ia") . "\r\n" .
      "_______________________________________________ " . "\r\n" .
      $string . "\r\n" .
      "===============================================" . "\r\n";

    $dir = $log_file_path . "/";
    $filename = $dir . date("m_d_Y") . "__mail.log";
    if (!file_exists($dir . "/")) {
      mkdir($dir, 0777, TRUE);
    }
    $fp = fopen($filename, 'a');
    $res1 = fwrite($fp, $string);
    $res2 = fclose($fp);

  } catch (Exception $e) {
    $error = $e->getMessage();
  }
}
