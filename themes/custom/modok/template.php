<?php
/**
 * Created by PhpStorm.
 * User: taki
 * Date: 2/6/17
 * Time: 11:58 AM
 */
define('WEBFORM_KEEP_ME_UPD_NID', '1');
define('WEBFORM_LEGISLATOR_CONTACT_NID', '2');
/**
 * Implements template_preprocess_html().
 */
function modok_preprocess_html(&$vars) {
  $node = menu_get_object();
  if (!empty($node)) {
    switch ($node->type) {
      case 'main':
        $vars['classes_array'][] = 'page page-home';
        break;
      case 'legislator_contacts':
        $vars['classes_array'][] = 'page page-email';
        break;

    }
  }
  if (empty($node) && !drupal_is_front_page()){
    $vars['classes_array'][] = 'page page-email';
  }
}

/**
 * Implements template_preprocess_page().
 */
function modok_preprocess_page(&$vars) {
  if (isset($vars['node']->type)) {
    $nodetype = $vars['node']->type;
    $vars['theme_hook_suggestions'][] = 'page__' . $nodetype;
  }
}

/**
 * Implements hook_preprocess_node().
 */
function modok_preprocess_node(&$vars) {
//  $node = menu_get_object();
  $node = $vars['node'];
//  kpr($node);

  if (!empty($node->field_main_sect_logo_img)) {
    $vars['section_logo_image'] = file_create_url($node->field_main_sect_logo_img[LANGUAGE_NONE][0]['uri']);
    hide($vars['content']['field_main_sect_logo_img']);
  }
  if (!empty($node->field_main_sect_faq_logo_img)) {
    $vars['section_faq_image'] = file_create_url($node->field_main_sect_faq_logo_img[LANGUAGE_NONE][0]['uri']);
    hide($vars['content']['field_main_sect_faq_logo_img']);
  }
  if (!empty($node->field_main_sect_items_first_img)) {
    $vars['section_contacts_first_image'] = file_create_url($node->field_main_sect_items_first_img[LANGUAGE_NONE][0]['uri']);
    hide($vars['content']['field_main_sect_items_first_img']);
  }
  if (!empty($node->field_main_sect_items_block_img)) {
    $vars['section_contacts_first_icon_image'] = file_create_url($node->field_main_sect_items_block_img[LANGUAGE_NONE][0]['uri']);
    hide($vars['content']['field_main_sect_items_block_img']);
  }
  if (!empty($node->field_main_sect_items_icon_img)) {
    $vars['section_contacts_second_icon_image'] = file_create_url($node->field_main_sect_items_icon_img[LANGUAGE_NONE][0]['uri']);
    hide($vars['content']['field_main_sect_items_icon_img']);
  }
  if (!empty($node->field_main_sect_items_snd_img)) {
    $vars['section_contacts_second_image'] = file_create_url($node->field_main_sect_items_snd_img[LANGUAGE_NONE][0]['uri']);
    hide($vars['content']['field_main_sect_items_snd_img']);
  }
}

function modok_field_collection_view($vars) {
  $element = $vars['element'];
  return $element['#children'];
}

/**
 * Implements hook_form_alter().
 */
function modok_form_alter(&$form, &$form_state, $form_id) {
  switch ($form_id) {
    case 'webform_client_form_' . WEBFORM_KEEP_ME_UPD_NID:
      $form['actions']['submit']['#attributes']['class'][] = 'button';
      break;
    case 'webform_client_form_' . WEBFORM_LEGISLATOR_CONTACT_NID:
      custom_wrap_item($form['submitted']['text'], 'text');
      custom_wrap_item($form['submitted']['zip'], 'form-item form-type-text width-20');
      custom_wrap_item($form['submitted']['address'], 'form-item form-type-text width-80');
      custom_wrap_item($form['submitted']['email'], 'form-item form-type-text');
      custom_wrap_item($form['submitted']['name'], 'form-item form-type-text');
      custom_wrap_item($form['submitted']['checkbox'], 'form-item form-type-checkbox');
      custom_wrap_item($form['submitted']['field_wrapper'], 'field-wrapper');
      custom_wrap_item($form['submitted']['captcha'], 'captcha');

      if(!empty($form['captcha']) && !empty($form['submitted']['mailchimp'])) {
        $form['submitted']['captcha'] = $form['captcha'];
        $form['submitted']['mailchimp']['#weight'] = $form['captcha']['#weight'] + 1;
        unset($form['captcha']);
      }
      break;
  }
}

/**
 * @param $element
 * @param $classes
 * @param string $tag
 *
 * funnel function for theme elements
 */
function custom_wrap_item(&$element, $classes, $tag = 'div') {
  if (!empty($element)) {
    $element['#prefix'] = '<' . $tag . (!empty($classes) ? ' class="' . $classes . '">' : '>') . (array_key_exists('#prefix', $element) ? $element['#prefix'] : '');
    $element['#suffix'] = (array_key_exists('#suffix', $element) ? $element['#suffix'] : '') . '</'. $tag . '>';
  }
}