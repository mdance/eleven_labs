<?php

namespace Drupal\eleven_labs\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\eleven_labs\Events\ElevenLabsAjaxResponseEvent;
use Drupal\eleven_labs\Events\ElevenLabsEvents;
use Drupal\eleven_labs\Events\ElevenLabsVoiceUpdateEvent;
use Drupal\eleven_labs\Exception\ElevenLabsException;
use Drupal\eleven_labs\HistoryInterface;
use Drupal\eleven_labs\Voice;
use Drupal\eleven_labs\VoiceInterface;
use Drupal\eleven_labs\ElevenLabsServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides the ElevenLabsController class.
 */
class ElevenLabsController extends ControllerBase {

  /**
   * {@inheritDoc}
   */
  public function __construct(
    protected EventDispatcherInterface $eventDispatcher,
    protected ElevenLabsServiceInterface $service
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('event_dispatcher'),
      $container->get('eleven_labs')
    );
  }

  /**
   * Gets the voice title.
   *
   * @param string $id
   *   Provides the id.
   *
   * @return string
   *   The voice title.
   */
  public function voiceTitle($voice) {
    if (is_string($voice)) {
      $voice = $this->service->voice($voice);
    }

    $string = 'Voice';

    $args = [];

    if ($voice instanceof VoiceInterface) {
      $id = $voice->getId();

      if ($id) {
        $args['@id'] = $id;

        $string .= ' @id';
      }
    }

    $string .= ' Details';

    return $this->t($string, $args);
  }

  /**
   * View a voice.
   *
   * @param VoiceInterface $voice
   *   Provides the voice.
   *
   * @return mixed
   *   The voice details.
   */
  public function view($voice) {
    return $voice->toRenderable();
  }

  /**
   * Gets the history title.
   *
   * @param string $id
   *   Provides the id.
   *
   * @return string
   *   The history title.
   */
  public function histortyTitle(HistoryInterface $history) {
    if (is_string($history)) {
      $history = $this->service->history($history);
    }

    $string = 'History';

    $args = [];

    if ($history instanceof HistoryInterface) {
      $id = $history->getId();

      if ($id) {
        $args['@id'] = $id;

        $string .= ' @id';
      }
    }

    $string .= ' Details';

    return $this->t($string, $args);
  }

  /**
   * View a history item.
   *
   * @param string $history
   *   Provides the history.
   *
   * @return mixed
   *   The history details.
   */
  public function viewHistory($history) {
    $result = $this->service->history($history);

    $content = $result->getBody();
    $status = $result->getStatusCode();
    $headers = $result->getHeaders();

    $output = new Response($content, $status, $headers);

    return $output;
  }

  /**
   * Downloads history items.
   *
   * @param string $history
   *   Provides the history.
   *
   * @return mixed
   *   The history details.
   */
  public function download($history) {
    $options = [];

    $options['ids'] = [$history];

    $result = $this->service->downloadHistory($options);

    $content = $result->getBody();
    $status = $result->getStatusCode();
    $headers = $result->getHeaders();

    $headers['Content-Disposition'] = "attachment; filename=\"$history.mp3\"";

    $output = new Response($content, $status, $headers);

    return $output;
  }

}
