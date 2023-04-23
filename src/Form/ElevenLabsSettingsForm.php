<?php

namespace Drupal\eleven_labs\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\State\StateInterface;
use Drupal\eleven_labs\ElevenLabsConstants;
use Drupal\eleven_labs\ElevenLabsServiceInterface;
use Drupal\eleven_labs\ElevenLabsUtilities;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the ElevenLabsSettingsForm class.
 */
class ElevenLabsSettingsForm extends ConfigFormBase {

  /**
   * {@inheritDoc}
   */
  public function __construct(
    ConfigFactoryInterface $configFactory,
    protected StateInterface $state,
    protected ModuleHandlerInterface $moduleHandler,
    protected ElevenLabsServiceInterface $service
  ) {
    parent::__construct($configFactory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('state'),
      $container->get('module_handler'),
      $container->get('eleven_labs')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'eleven_labs_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      ElevenLabsConstants::KEY_SETTINGS
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $form = parent::buildForm($form, $form_state);

    $key = ElevenLabsConstants::KEY_SCHEMA;

    $default_value = $this->service->getSchema();

    $form[$key] = [
      '#type' => 'textfield',
      '#title' => $this->t('Schema'),
      '#default_value' => $default_value,
    ];

    $key = ElevenLabsConstants::KEY_HOST;

    $default_value = $this->service->getHost();

    $form[$key] = [
      '#type' => 'textfield',
      '#title' => $this->t('Host'),
      '#default_value' => $default_value,
    ];

    $key = ElevenLabsConstants::KEY_API_KEY;

    $default_value = $this->service->getApiKey();

    $form[$key] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $default_value,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->cleanValues()->getValues();

    $config = $this->config(ElevenLabsConstants::KEY_SETTINGS);

    $keys = [
      ElevenLabsConstants::KEY_API_KEY
    ];

    $state = $this->state->get(ElevenLabsConstants::KEY_STATE, []);

    foreach ($keys as $key) {
      $result = $values[$key] ?? NULL;
      unset($values[$key]);

      if ($result === NULL) {
        continue;
      }

      $state[$key] = $result;
    }

    $this->state->set(ElevenLabsConstants::KEY_STATE, $state);

    foreach ($values as $key => $value) {
      $config->set($key, $value);
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
