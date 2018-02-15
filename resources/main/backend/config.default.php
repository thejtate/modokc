<?php
/**
 * Config example. Rename to config.php
 */

return array(
  'mail_to' => '',//Test email
  //'mail_to' => '', //Real Email.
  'mail_is_smtp' => FALSE,
//  'mail_smtp_host' => 'smtp.gmail.com', //use only if mail_is_smtp = TRUE
//  'mail_smtp_username' => 'SMTP_USERNAME',//use only if mail_is_smtp = TRUE
//  'mail_smtp_password' => 'SMTP_PASS',//use only if mail_is_smtp = TRUE
  'mail_from' => '',
  'mail_error_sending' => 'Error sending email. Please contact site administrator.',
  'mail_success_message' => 'Your message was sent successfully.',
  'error_captcha' => 'Error: Invalid captcha',
);