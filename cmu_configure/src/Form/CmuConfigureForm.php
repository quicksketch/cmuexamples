<?php

namespace Drupal\cmu_configure\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Configure global settings for this site.
 */
class CmuConfigureForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cmu_configure_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'system.site',
      // Other configuration files example:
      //'user.mail',
      //'user.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $site_config = $this->config('system.site');

    // Other configuration files example:
    //$user_config = $this->config('user.settings');
    //$mail_config = $this->config('user.mail');

    // Attaching a library:
    //$form['#attached']['library'][] = 'user/drupal.user.admin';

    // Example form:
    $form['site_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site name'),
      '#default_value' => $site_config->get('name'),
      '#description' => $this->t('The site name.'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('system.site')
      ->set('name', $form_state->getValue('site_name'))
      ->save();
  }
}
