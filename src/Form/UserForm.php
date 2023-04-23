<?php

namespace Drupal\eleven_labs\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\eleven_labs\ElevenLabsServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the UserForm class.
 */
class UserForm extends FormBase {

  /**
   * {@inheritDoc}
   */
  public function __construct(
    protected ElevenLabsServiceInterface $service,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('eleven_labs'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'eleven_labs_user_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $results = $this->service->getUserInfo();

    $subscription = $results->subscription;

    $key = 'subscription';

    $form[$key] = [
      '#type' => 'details',
      '#title' => $this->t('Subscription'),
      '#open' => TRUE,
    ];

    $group = &$form[$key];

    $group['tier'] = [
      '#type' => 'item',
      '#title' => $this->t('Tier'),
      '#markup' => ucwords($subscription->tier),
    ];

    $group['character_count'] = [
      '#type' => 'item',
      '#title' => $this->t('Character Count'),
      '#markup' => $subscription->character_count,
    ];

    $group['character_limit'] = [
      '#type' => 'item',
      '#title' => $this->t('Character Limit'),
      '#markup' => $subscription->character_limit,
    ];

    $group['extend'] = [
      '#type' => 'item',
      '#title' => $this->t('Extendable'),
      '#markup' => $subscription->can_extend_character_limit,
    ];

    try {
      $date = DrupalDateTime::createFromTimestamp($subscription->next_character_count_reset_unix);
      $markup = $date->format('Y-m-d');

      $group['reset'] = [
        '#type' => 'item',
        '#title' => $this->t('Reset Date'),
        '#markup' => $markup,
      ];
    } catch (\Exception $e) {}

    $group['voice_limit'] = [
      '#type' => 'item',
      '#title' => $this->t('Voice Limit'),
      '#markup' => $subscription->voice_limit,
    ];

    $group['professional_voice_limit'] = [
      '#type' => 'item',
      '#title' => $this->t('Professional Voice Limit'),
      '#markup' => $subscription->professional_voice_limit,
    ];

    if ($subscription->can_extend_voice_limit) {
      $markup = $this->t('Yes');
    }
    else {
      $markup = $this->t('No');
    }

    $group['voice_limit_extend'] = [
      '#type' => 'item',
      '#title' => $this->t('Voice Limit Extendable'),
      '#markup' => $markup,
    ];

    if ($subscription->can_use_instant_voice_cloning) {
      $markup = $this->t('Yes');
    }
    else {
      $markup = $this->t('No');
    }

    $group['voice_cloning'] = [
      '#type' => 'item',
      '#title' => $this->t('Voice Cloning'),
      '#markup' => $markup,
    ];

    if ($subscription->can_use_professional_voice_cloning) {
      $markup = $this->t('Yes');
    }
    else {
      $markup = $this->t('No');
    }

    $group['professional_voice_cloning'] = [
      '#type' => 'item',
      '#title' => $this->t('Professional Voice Cloning'),
      '#markup' => $markup,
    ];

    $header = [];

    $header['id'] = $this->t('ID');
    $header['name'] = $this->t('Name');
    $header['languages'] = $this->t('Languages');

    $rows = [];

    foreach ($subscription->available_models as $model) {
      $row = [];

      $row['id'] = $model->model_id;
      $row['name'] = $model->display_name;

      $languages = [];

      foreach ($model->supported_language as $language) {
        $languages[] = $language->display_name;
      }

      $row['languages'] = implode(', ', $languages);

      $rows[] = $row;
    }

    $group['available_models'] = [
      '#type' => 'item',
      '#title' => $this->t('Available Models'),
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ],
    ];

    if ($subscription->can_use_delayed_payment_methods) {
      $markup = $this->t('Yes');
    }
    else {
      $markup = $this->t('No');
    }

    $group['can_use_delayed_payment_methods'] = [
      '#type' => 'item',
      '#title' => $this->t('Delayed Payment Methods'),
      '#markup' => $markup,
    ];

    if (!empty($subscription->currency)) {
      $group['currency'] = [
        '#type' => 'item',
        '#title' => $this->t('Currency'),
        '#markup' => $subscription->currency,
      ];
    }

    $group['status'] = [
      '#type' => 'item',
      '#title' => $this->t('Status'),
      '#markup' => ucwords($subscription->status),
    ];

    $form['new_user'] = [
      '#type' => 'item',
      '#title' => $this->t('New User'),
      '#markup' => ucwords($results->is_new_user),
    ];

    $form['api_key'] = [
      '#type' => 'item',
      '#title' => $this->t('API Key'),
      '#markup' => $results->xi_api_key,
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
