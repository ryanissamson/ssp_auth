<?php

/**
 * @file
 * Contains \Drupal\ssp_auth\Form\SSPAuthSettings.
 */

namespace Drupal\ssp_auth\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class SSPAuthSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ssp_auth_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ssp_auth.settings');

    foreach (Element::children($form) as $variable) {
      $config->set($variable, $form_state->getValue($form[$variable]['#parents']));
    }
    $config->save();

    if (method_exists($this, '_submitForm')) {
      $this->_submitForm($form, $form_state);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ssp_auth.settings'];
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {

    $form['ssp_auth_setup'] = [
      '#type' => 'fieldset',
      '#title' => t('Basic Setup'),
      '#collapsible' => FALSE,
    ];
    $form['ssp_auth_setup']['ssp_auth_installdir'] = [
      '#type' => 'textfield',
      '#title' => t('Installation directory (default: /var/simplesamlphp)'),
      '#default_value' => \Drupal::config('ssp_auth.settings')->get('ssp_auth_installdir'),
      '#description' => t('The base directory of simpleSAMLphp. Absolute path with no trailing slash.'),
    ];
    $form['ssp_auth_setup']['ssp_auth_authsource'] = [
      '#type' => 'textfield',
      '#title' => t('Authentication source (The one that uses the drupalauth:External class)'),
      '#default_value' => \Drupal::config('ssp_auth.settings')->get('ssp_auth_authsource'),
      '#description' => t('The simpleSAMLphp authentication source.'),
    ];

    return parent::buildForm($form, $form_state);
  }

}
