<?php

namespace Drupal\eleven_labs;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\PrivateKey;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\eleven_labs\Events\ElevenLabsEvents;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides the ElevenLabsService class.
 */
class ElevenLabsService implements ElevenLabsServiceInterface {

  use StringTranslationTrait;

  /**
   * Provides the module configuration.
   */
  protected Config $config;

  /**
   * Provides the request.
   */
  protected Request $request;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected StateInterface $state,
    protected ClientInterface $client,
    protected PrivateKey $privateKey,
    protected Connection $connection,
    protected RequestStack $requestStack,
    protected ModuleHandlerInterface $moduleHandler,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EventDispatcherInterface $eventDispatcher,
    protected ElevenLabsApiInterface $api
  ) {
    $this->config = $configFactory->getEditable(ElevenLabsConstants::KEY_SETTINGS);
    $this->request = $requestStack->getCurrentRequest();

    $configuration = $this->config->getRawData();
    $state = $this->state->get(ElevenLabsConstants::KEY_STATE);

    $configuration = array_merge($configuration, $state);

    $configuration['client'] = $client;

    $this->api->setConfiguration($configuration);
  }

  /**
   * {@inheritDoc}
   */
  public function getSchema(): string {
    return $this->config->get(ElevenLabsConstants::KEY_SCHEMA) ?? ElevenLabsConstants::SCHEMA;
  }

  /**
   * {@inheritDoc}
   */
  public function getHost(): string {
    return $this->config->get(ElevenLabsConstants::KEY_HOST) ?? ElevenLabsConstants::HOST;
  }

  /**
   * {@inheritDoc}
   */
  public function getApiKey(): string {
    $state = $this->state->get(ElevenLabsConstants::KEY_STATE);

    return $state[ElevenLabsConstants::KEY_API_KEY] ?? '';
  }

  /**
   * {@inheritDoc}
   */
  public function createVoice(array $options = []): VoiceInterface {
    $output = $this->api->createVoice($options);

    return $output;
  }

  /**
   * {@inheritDoc}
   */
  public function voices(): array {
    return $this->api->voices();
  }

  /**
   * {@inheritDoc}
   */
  public function voice($id): VoiceInterface {
    return $this->api->voice($id);
  }

  /**
   * {@inheritDoc}
   */
  public function deleteVoice(string $id): bool {
    return $this->api->deleteVoice($id);
  }

  /**
   * {@inheritDoc}
   */
  public function getVoiceOptions(): array {
    $output = [];

    try {
      $results = $this->voices();

      foreach ($results as $result) {
        $output[$result->getId()] = $result->getName();
      }
    } catch (\Exception $e) {
    }

    return $output;
  }

  /**
   * {@inheritDoc}
   */
  public function getDefaultVoice(): string {
    $output = $this->config->get('default_voice') ?? NULL;

    try {
      $results = $this->getVoiceOptions();

      if (!is_null($output) && isset($results[$output])) {
        return $output;
      }

      foreach ($results as $key => $value) {
        return $key;
      }
    } catch (\Exception $e) {
    }

    return $output;
  }

  /**
   * {@inheritDoc}
   */
  public function textToSpeech(array $options) {
    return $this->api->textToSpeech($options);
  }

  /**
   * {@inheritDoc}
   */
  public function histories(): array {
    return $this->api->histories();
  }

  /**
   * {@inheritDoc}
   */
  public function history(string $id): Response {
    return $this->api->history($id);
  }

  /**
   * {@inheritDoc}
   */
  public function deleteHistory(string $id): bool {
    return $this->api->deleteHistory($id);
  }

  /**
   * {@inheritDoc}
   */
  public function downloadHistory(array $options): mixed {
    return $this->api->downloadHistory($options);
  }

  /**
   * {@inheritDoc}
   */
  public function getUserInfo(): object {
    return $this->api->getUserInfo();
  }
}
