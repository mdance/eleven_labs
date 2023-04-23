<?php

namespace Drupal\eleven_labs\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\eleven_labs\ElevenLabsServiceInterface;
use Drupal\eleven_labs\VoiceInterface;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the DeleteVoiceForm class.
 */
class DeleteVoiceForm extends ConfirmFormBase {

  /**
   * Provides the item.
   */
  protected VoiceInterface $item;

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
    return 'eleven_labs_delete_voice_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, VoiceInterface $voice = NULL) {
    $this->item = $voice;

    $form['item'] = [
      '#type' => 'value',
      '#value' => $voice,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public function getQuestion() {
    $args = [];

    $args['@name'] = $this->item->getName();
    $args['@id'] = $this->item->getId();

    return $this->t('Are you sure you want to delete the voice @name (@id)?', $args);
  }

  /**
   * {@inheritDoc}
   */
  public function getCancelUrl() {
    $route_name = 'eleven_labs.voice.view';

    $route_parameters = [];

    $route_parameters['voice'] = $this->item->getId();

    return Url::fromRoute($route_name, $route_parameters);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $item = $this->item;

    $id = $item->getId();

    $route_parameters = [];

    if (!$id) {
      return;
    }

    $reason = '';

    try {
      $result = $this->service->deleteVoice($id);
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

    $args['@name'] = $item->getName();
    $args['@id'] = $id;
    $args['@reason'] = $reason;

    if (!$result) {
      $message = $this->t('An error occurred attempting to delete the @name voice (@id).', $args);

      if (!empty($reason)) {
        $message .= '  ' . $reason;
      }
    } else {
      $message = $this->t('Voice @name (@id) has been deleted.', $args);
    }

    $route_name = 'eleven_labs.voices';

    $this->messenger()->addMessage($message);

    $form_state->setRedirect($route_name, $route_parameters);
  }

}
