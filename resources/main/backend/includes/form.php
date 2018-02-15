<?php
/**
 * @file processing form submit, validate, captcha
 */

define('EMAILS_LIST_FILE_PATH', __DIR__  . '/../emails_list.json');

include_once(__DIR__ . '/log.php');

function get_config() {
  return require(__DIR__ . '/../config.php');
}


function contact_form() {
  return array(
    'submit_button_name' => 'contact_submit',
    'logs_folder' => 'contact',
    'mail_title' => 'Modernize oklahoma. "Contact" form submit.',
    'fields' => array(
        'name' => array('label' => 'Name', 'validate' => array('required')),
        'email' => array('label' => 'Email', 'validate' => array('email', 'required')),
        'address' => array('label' => 'Address', 'validate' => array()),
        'zip' => array('label' => 'Zip', 'validate' => array('required', 'digits', 'size' => 5)),
        'send_me' => array('label' => 'Send me emails about this campaign', 'validate' => array(), 'exclude_from_email' => TRUE),
    )
  );
}

function process_form($form) {
  $messages = array();
  $success_message_exist = FALSE;

  $submit_button_name = $form['submit_button_name'];
  if (isset($_POST[$submit_button_name])) {
    $conf = get_config();
    $fields = $form['fields'];

    $messages = array_merge($messages, validate_form($fields));
    //if don`t have validation messages than send email
    if(empty($messages)) {

      if (function_exists("prepareMailLog")) {
        prepareMailLog($fields, PATH_TO_MAIL_LOG_FILE . $form['logs_folder'] . '/');
      }

      $email_message = get_form_mail_message($fields);

      $receivers = get_reciever_emails_by_zip((string) $_POST['zip']);
      if ($receivers) {
        foreach ($receivers as $receiver) {
          $send_message = send_email(
            $email_message,
            $receiver['email'],
            $form['mail_title']
          );
          //Add success message only once.
          if (!$success_message_exist && !empty($send_message['status']) && $send_message['status'] == 'success') {
            $messages = array_merge($messages, $send_message);
            $success_message_exist = TRUE;
          }
        }
      } else {
        $messages = array('Please enter a valid Oklahoma zip code', 'status' => 'error', 'field' => 'zip');
        return $messages;
      }

      //send confirmation message to submitter
      if(!empty($_POST['email'])) {
        $email_message = '<h1>Thank you for your submission!</h1>' . $email_message;
        send_email($email_message, $_POST['email'], $form['mail_title']);
      }

    } else {
      $messages['status'] = 'error';
    }
  }

  if($success_message_exist) {
    page_redirect('/thank-you.php');
  }

  return $messages;
}

/**
 * Get associated emails list by zip code. From emails_list data file.
 *
 * @param $zip
 * @return mixed bool, array emails with names
 */
function get_reciever_emails_by_zip($zip) {

  $zip_emails_list = file_get_contents(EMAILS_LIST_FILE_PATH);

  $zip_emails_list = json_decode($zip_emails_list, true);

  return isset($zip_emails_list[$zip]) ? $zip_emails_list[$zip] : FALSE;
}

/**
 * Check form submit
 * @return array
 */
function validate_form($fields) {
  $conf = get_config();
  $messages = array();
  if (empty($_SESSION['captcha']) || strtolower(trim($_REQUEST['captcha'])) != $_SESSION['captcha']) {
    $messages[] = $conf['error_captcha'];
  }

  $group_required = array();

  foreach($fields as $key => $info) {

    if(!isset($info['validate'])) {
      continue; //skip this field
    }

    $value = !empty($_POST[$key]) ? trim((string) $_POST[$key]) : '';

    //check required fields
    if(in_array('required' ,$info['validate'])) {
      if(empty($value)) {
        $messages[] = 'Error: field "' . $info['label'] . '" is required.';
      }
    }
    //check email validation
    if(in_array('email' ,$info['validate'])) {
      if(!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $messages[] = 'Error: field "' . $info['label'] . '" has invalid email format.';
      }
    }

    //check group_required validation
    if(in_array('group_required' ,$info['validate'])) {
      $group_required[$key] = $info;
    }

    //check digits validation
    if(in_array('digits' ,$info['validate'])) {
      if(!empty($value) && filter_var($value, FILTER_VALIDATE_INT) === FALSE) {
        $messages[] = 'Error: field "' . $info['label'] . '". Please enter only digits.';
      }
    }
    //check value size
    if(isset($info['validate']['size'])) {
      if(!empty($value) && strlen($value) != $info['validate']['size']) {
        $messages[] = 'Error: field "' . $info['label'] . '". Please enter ' . $info['validate']['size'] . ' characters.';
      }
    }

    //checkboxes checkboxes_at_least_one_required validation
    if(in_array('checkboxes_at_least_one_required' ,$info['validate']) && !empty($info['names'])) {
      $all_chexboxes_empty = TRUE;
      foreach ($info['names'] as $name) {
        if(!empty($_POST[$name])) {
          $all_chexboxes_empty = FALSE;
          break;//exit from foreach
        }
      }
      if($all_chexboxes_empty) {
        $messages[] = 'Error: "' . $info['label'] . '" is required.';
      }
    }
  }

  // validate group_required fields. At least one field from the group_required should be not empty
  if(!empty($group_required)) {
    $all_group_empty = TRUE;
    foreach ($group_required as $key => $info) {
      $value = !empty($_POST[$key]) ? trim((string) $_POST[$key]) : '';
      if(!empty($value)) {
        $all_group_empty = FALSE;
        break;//stop foreach
      }
    }

    if($all_group_empty) {
      $message = 'Error: Please fill in at least one field from the list ';
      foreach ($group_required as $info) {
        $message .= '"' . $info['label'] . '" ';
      }
      $messages[] = $message;
    }
  }

  return $messages;
}

function send_email($message, $to, $subject) {
  $conf = get_config();

  if(!$conf['mail_is_smtp']) {
    return send_php_email($message, $to, $subject);
  } else {
    //smtp send mail
    return send_smtp_email($message, $to, $subject);
  }

}

function send_php_email($message, $to, $subject) {
  $conf = get_config();

  $headers = 'From: ' . $conf['mail_from'] . "\r\n" .
    'Reply-To: ' . $conf['mail_from'] . "\r\n" .
    'MIME-Version: 1.0' . "\r\n" .
    'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

  $r = mail($to, $subject, $message, $headers);

  if ($r) {
    return array($conf['mail_success_message'], 'status' => 'success');
  }
  else {
    return array($conf['mail_error_sending'], 'status' => 'error');
  }
}

function send_smtp_email($message, $to, $subject) {
  require_once(__DIR__ . '/../libraries/PHPMailer/PHPMailerAutoload.php');
  $conf = get_config();
  $mail = new PHPMailer;

  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->Host = $conf['mail_smtp_host'];  // Specify main and backup server
  $mail->SMTPAuth = true;                               // Enable SMTP authentication
  $mail->Username = $conf['mail_smtp_username'];                            // SMTP username
  $mail->Password = $conf['mail_smtp_password'];                           // SMTP password
  $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

  $mail->From = $conf['mail_from'];
  $mail->FromName = 'Miller';
  $mail->addAddress($to);  // Add a recipient
  $mail->addReplyTo($conf['mail_from'], 'Miller');

  $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
  $mail->isHTML(true);                                  // Set email format to HTML

  $mail->Subject = $subject;
  $mail->Body    = $message;

  if($mail->send()) {
    return array($conf['mail_success_message'], 'status' => 'success');
  } else {
    return array($conf['mail_error_sending'], 'status' => 'error');
  }

}

/**
 * Prepare mail message
 * @return string
 */
function get_form_mail_message($fields) {

  $message = '<html><body>';
  $message .= '<p>Dear Official,</p>';
  $message .= '<p>I am writing to urge you to vote YES on SJR 68. I support modern alcohol laws, and I believe Oklahomans should be given a chance to vote on this issue.</p>';
  $message .= '<p>Modernizing Oklahoma\'s alcohol laws includes modernizing Oklahoma\'s distribution system. Don\'t let special interest groups derail modernization by amending SJR 68. Say "Yes" to SJR 68 in its current form and say "No" to amendments that include government mandates requiring manufacturers to sell to every wholesaler.</p>';
  $message .= '<table rules="all" style="border-color: #fff;" cellpadding="10">';

  foreach ($fields as $key => $field) {
    if(!empty($field['exclude_from_email'])) {
      continue;
    }
    if(!empty($field['type']) && $field['type'] == 'checkboxes') { //checkboxes field
      $all_chexboxes_empty = TRUE;
      foreach ($field['names'] as $checkobox_name) {
        if(!empty($_POST[$checkobox_name])) {
          $all_chexboxes_empty = FALSE;
          break;//exit foreach
        }
      }

      if(!$all_chexboxes_empty) {
        $message .= '<tr><td colspan="2"><b>' .$field["label"] . ':</b></td>';
        foreach ($field['names'] as $checkobox_name) {
          $message .= !empty($_POST[$checkobox_name]) ? '<tr><td colspan="2">' . htmlspecialchars(
              $_POST[$checkobox_name]
            ) . '</td>' : '';
        }
      }

    } else { // not checkboxes
      $message .= '<tr><td><b>' .$field['label'] . ':</b></td><td>' . htmlspecialchars(isset($_POST[$key]) ? $_POST[$key] : '') . '</td>';
    }
  }

  $message .= "</table>";
  $message .= '</body></html>';

  return $message;
}

/**
 * Dev function for testing
 */
function dump($var, $die = FALSE) {
  echo '<pre>';
  print_r($var);
  echo '</pre>';
  if($die) {
    die();
  }
}

/**
 * Fill value only if we have error on submit
 * @param $field_key
 * @param $submit_messages
 * @param string $type  'input' or 'textarea'
 */
function print_value($field_key, $submit_messages, $type = 'input') {

  $is_error = !empty($submit_messages['status']) && $submit_messages['status'] == 'error';
  if($is_error) {
    $value = htmlspecialchars(isset($_POST[$field_key]) ? $_POST[$field_key] : '');
    if($type == 'input') {
      echo 'value="' . $value .  '"';
    } elseif($type == 'textarea') {
      echo $value;
    }

  }
}

/**
 * Check if field has errors, and print error class
 */
function print_error_class($field_key, $submit_messages, $type = 'input') {

  $is_error = !empty($submit_messages['status']) && $submit_messages['status'] == 'error';

  if($is_error && !empty($submit_messages['field']) && $submit_messages['field'] == $field_key) {
    echo 'error';
  } else {
    echo '';
  }
}

function print_checkbox_selected($field_key, $submit_messages) {
  $is_error = !empty($submit_messages['status']) && $submit_messages['status'] == 'error';
  if($is_error && !empty($_POST[$field_key])) {
    echo 'checked';
  }
}

function print_states_options($field_key, $submit_messages) {
  $conf = get_config();
  $states = $conf['usa_states'];
  $is_error = !empty($submit_messages['status']) && $submit_messages['status'] == 'error';

  foreach ($states as $state) {
    if($is_error && ((string) $_POST[$field_key]) === $state) {
      echo '<option selected>' . $state . '</option>';
    } else {
      echo '<option>' . $state . '</option>';
    }
  }
}

function page_redirect($url) {
  ob_start(); // ensures anything dumped out will be caught

// clear out the output buffer
  while (ob_get_status())
  {
    ob_end_clean();
  }

  header( "Location: $url" );
  exit();
}