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
      'system.theme.global',
      //'user.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $site_config = $this->config('system.site');
    $theme_config = $this->config('system.theme.global');

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

    $form['logo']['settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Logo settings'),
      '#open' => TRUE,
    ];
    $form['logo']['settings']['logo_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path to custom logo'),
      '#default_value' => $theme_config->get('logo.path'),
    ];
    $form['logo']['settings']['logo_upload'] = [
      '#type' => 'file',
      '#title' => t('Upload logo image'),
      '#maxlength' => 40,
      '#description' => t("If you don't have direct file access to the server, use this field to upload your logo."),
      '#upload_validators' => [
        'file_validate_is_image' => [],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->messenger()->addStatus($this->t('Thank you for submitting the form!'));

    $this->config('system.site')
      ->set('name', $form_state->getValue('site_name'))
      ->save();


    // If the user uploaded a new logo or favicon, save it to a permanent location
    // and use it in place of the default theme-provided file.
    $values = $form_state->getValues();
    if (!empty($values['logo_upload'])) {
      $filename = file_unmanaged_copy($values['logo_upload']->getFileUri());
      $values['default_logo'] = 0;
      $values['logo_path'] = $filename;
    }
    unset($values['logo_upload']);

    // If the user entered a path relative to the system files directory for
    // a logo or favicon, store a public:// URI so the theme system can handle it.
    if (!empty($values['logo_path'])) {
      $values['logo_path'] = $this->validatePath($values['logo_path']);
    }

    $theme_config = $this->config('system.theme.global');
    theme_settings_convert_to_config($values, $theme_config)->save();
  }
}
