<?php

namespace Drupal\eleven_labs\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\eleven_labs\ElevenLabsServiceInterface;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the DeleteHistoryForm class.
 */
class DeleteHistoryForm extends ConfirmFormBase {

  /**
   * Provides the item.
   */
  protected string $item;

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
    return 'eleven_labs_delete_history_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, string $history = NULL) {
    $this->item = $history;

    $form['item'] = [
      '#type' => 'value',
      '#value' => $history,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public function getQuestion() {
    $args = [
      '@id' => $this->item,
    ];

    return $this->t('Are you sure you want to delete the history @id?', $args);
  }

  /**
   * {@inheritDoc}
   */
  public function getCancelUrl() {
    $route_name = 'eleven_labs.history';

    return Url::fromRoute($route_name);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $item = $this->item;

    if (!$item) {
      return;
    }

    $route_parameters = [];

    $reason = '';

    try {
      $result = $this->service->deleteHistory($item);
    } catch (\Exception $e) {
      $p = $e->getPrevious();

      if ($p) {
        $p2 = $p->getPrevious();

        if ($p2 instanceof ClientException) {
          $response = $p2->getResponse();

          if ($response) {
            $result = (string)$response->getBody();

            if (!empty($result)) {
              $result = json_decode($result);

              $reason = $result?->detail?->message;

              $this->logger('eleven_labs')->error($reason);
            }
          }
        }
      }

      watchdog_exception('eleven_labs', $e);

      $result = FALSE;
    }

    $args = [];

    $args['@id'] = $item;
    $args['@reason'] = $reason;

    if (!$result) {
      $message = $this->t('An error occurred attempting to delete history (@id).', $args);

      if (!empty($reason)) {
        $message .= '  ' . $reason;
      }
    } else {
      $message = $this->t('History @id has been deleted.', $args);
    }

    $route_name = 'eleven_labs.history';

    $this->messenger()->addMessage($message);

    $form_state->setRedirect($route_name, $route_parameters);
  }

}
