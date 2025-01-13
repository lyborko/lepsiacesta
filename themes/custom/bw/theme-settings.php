<?php

declare(strict_types=1);

/**
 * @file
 * Theme settings form for bw theme.
 */

use Drupal\Core\Form\FormState;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function bw_form_system_theme_settings_alter(array &$form, FormState $form_state): void {

  $form['bw'] = [
    '#type' => 'details',
    '#title' => t('bw'),
    '#open' => TRUE,
  ];

  $form['bw']['example'] = [
    '#type' => 'textfield',
    '#title' => t('Example'),
    '#default_value' => theme_get_setting('example'),
  ];

}
