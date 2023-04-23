<?php

namespace Drupal\eleven_labs;

/**
 * Provides the ElevenLabsServiceInterface interface.
 */
interface ElevenLabsServiceInterface {

  /**
   * Gets the schema.
   *
   * @return string
   *   A string containing the schema.
   */
  public function getSchema(): string;

  /**
   * Gets the host.
  *
   * @return string
   *   A string containing the host.
   */
  public function getHost(): string;

  /**
   * Gets the API key.
   *
   * @return string
   *   A string containing the API key.
   */
  public function getApiKey(): string;

  /**
   * Creates a voice.
   *
   * @param array $options
   *   An array of options.
   *
   * @return \Drupal\eleven_labs\VoiceInterface
   *   The voice object.
   */
  public function createVoice(array $options = []): VoiceInterface;

  /**
   * Gets the voices.
   *
   * @return \Drupal\eleven_labs\VoiceInterface[]
   *   An array of voices.
   */
  public function voices(): array;

  /**
   * Gets a voice.
   *
   * @param string $id
   *   A string containing the id.
   *
   * @return \Drupal\eleven_labs\VoiceInterface
   *   The voice object.
   */
  public function voice($id): VoiceInterface;

  /**
   * Deletes a voice.
   *
   * @param string $id
   *   A string containing the id.
   *
   * @return bool
   *   A boolean indicating if the voice was deleted.
   */
  public function deleteVoice(string $id): bool;

  /**
   * Gets the voice options.
   *
   * @return array
   *   An array of voice options.
   */
  public function getVoiceOptions(): array;

  /**
   * Gets the default voice.
   *
   * @return string
   *   The default voice id.
   */
  public function getDefaultVoice(): string;

  /**
   * Converts text to speech.
   *
   * @param array $options
   *   An array of options.
   *
   * @return array
   *   The text to speech result.
   */
  public function textToSpeech(array $options);

  /**
   * Gets the histories.
   *
   * @return \Drupal\eleven_labs\HistoryInterface[]
   *   An array of history.
   */
  public function histories(): array;

  /**
   * Gets a history.
   *
   * @param string $id
   *   A string containing the id.
   *
   * @return \GuzzleHttp\Psr7\Response
   *   The history object.
   */
  public function history(string $id);

  /**
   * Deletes a history.
   *
   * @param string $id
   *   A string containing the id.
   *
   * @return bool
   *   A boolean indicating if the history was deleted.
   */
  public function deleteHistory(string $id): bool;

  /**
   * Downloads history.
   *
   * @param array $options
   *   An array of options.
   *
   * @return mixed
   *   The download response
   */
  public function downloadHistory(array $options): mixed;

  /**
   * Gets the user info.
   *
   * @return object
   *   The user info
   */
  public function getUserInfo(): object;

}
