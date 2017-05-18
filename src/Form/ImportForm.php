<?php

namespace Drupal\default_content_deploy\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @file
 *
 * Created by PhpStorm.
 * User: Marty
 * Date: 18. 5. 2017
 * Time: 16:05
 */
class ImportForm extends FormBase  {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dcd_import_form';
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['default_content_deploy.import'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['force-update'] = [
      '#type' => 'checkbox',
      '#title' => t('Force update'),
      '#description' => $this->t('Import content but existing content with different UUID will be replaced (recommended for better content synchronization).'),
      '#default_value' => true,
    ];

    $form['import'] = [
      '#type' => 'submit',
      '#value' => t('Import content'),
    ];

    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $forceUpdate = $form_state->getValue('force-update', FALSE);
    $result_info = \Drupal::service('default_content_deploy.importer')->deployContent($forceUpdate, TRUE);
    $message = t('@count entities have been imported.', ['@count' => $result_info['processed']]);
    $message .= " ";
    $message .= t('created: @count, ', ['@count' => $result_info['created']]);
    $message .= t('updated: @count, ', ['@count' => $result_info['updated']]);
    $message .= t('skipped: @count', ['@count' => $result_info['skipped']]);
    drupal_set_message($message);
  }
}