<?php

namespace Drupal\eleven_labs;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

class ElevenLabsApi implements ElevenLabsApiInterface {

  /**
   * Provides the client.
   */
  protected ?ClientInterface $client = NULL;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    protected array $configuration = []
  ) {
  }

  public function getDefaultConfiguration() {
    return [
      'schema' => ElevenLabsConstants::SCHEMA,
      'host' => ElevenLabsConstants::HOST,
      'api_key' => '',
      'client' => NULL,
    ];
  }

  public function getConfiguration() {
    return $this->configuration;
  }

  public function setConfiguration($input = []) {
    $defaults = $this->getDefaultConfiguration();

    $input = array_merge($defaults, $input);

    $this->configuration = $input;

    return $this;
  }

  public function getUrl() {
    $configuration = $this->getConfiguration();

    $schema = $configuration['schema'];
    $host = $configuration['host'];

    return "$schema://$host";
  }

  public function getApiKey() {
    $configuration = $this->getConfiguration();

    return $configuration['api_key'];
  }

  public function getClient() {
    if (!$this->client) {
      $configuration = $this->getConfiguration();

      $this->client = $configuration['client'];
    }

    return $this->client;
  }

  public function getUserAgent(array $context = []) {
    return 'ElevenLabsApi client';
  }

  public function getContentType(array $context = []) {
    return 'application/json';
  }

  public function getAccept(array $context = []) {
    return 'application/json';
  }

  public function getContentLength(array $context = []) {
    return 0;
  }

  public function getAcceptEncoding(array $context = []) {
    return 'gzip,deflate';
  }

  public function getHeaderMap(array $context = []) {
    $output = [
      'User-Agent' => [
        $this,
        'getUserAgent',
      ],
      'Content-Type' => [
        $this,
        'getContentType',
      ],
      'Accept' => [
        $this,
        'getAccept',
      ],
      'Content-Length' => [
        $this,
        'getContentLength',
      ],
      'Accept-Encoding' => [
        $this,
        'getAcceptEncoding',
      ],
    ];

    return $output;
  }

  public function getHeaders(array $context = []) {
    $output = [];

    $map = $this->getHeaderMap($context);

    foreach ($map as $src => $callable) {
      $result = $callable($context);

      if (!empty($result)) {
        $output[$src] = $result;
      }
    }

    return $output;
  }

  public function getAuthenticatedHeaders(array $context = []) {
    $output = $this->getHeaders();

    $output[ElevenLabsConstants::KEY_API_KEY_HEADER] = $this->getApiKey();

    return $output;
  }

  public function sendRequest(array $options = []) {
    $defaults = [
      'url' => '',
      'method' => 'GET',
      'headers' => [],
      'body' => [],
    ];

    $options = array_merge($defaults, $options);

    $url = $options['url'];
    $headers = $options['headers'];
    $method = $options['method'];
    $body = $options['body'];

    return $this->sendHttpRequest($url, $headers, $method, $body);
  }

  public function sendHttpRequest($url, array $headers, string $method = 'GET', array $body = []) {
    $response = NULL;

    try {
      $params['headers'] = $headers;

      if (!empty($body)) {
        $params['body'] = $this->processBody($body);
      }

      $client = $this->getClient();

      return $client->request($method, $url, $params);
    }
    catch (\Exception $exception) {
      $message = 'An error occurred sending an API request.';
      $code = $this->getExceptionCode(__METHOD__);

      throw New \Exception($message, $code, $exception);
    }
  }

  public function processBody($body) {
    return json_encode($body);
  }

  public function getAuthenticatedRequest($endpoint, &$url, &$headers = [], $query = NULL) {
    try {
      $url = $this->getUrl();
      $url .= $endpoint;

      if (!is_null($query)) {
        if (is_array($query)) {
          $query = http_build_query($query);
        }

        if (substr($query, 0, 1) != '?') {
          $query = '?' . $query;
        }

        $url .= $query;
        $url = rtrim($url, '?');
      }

      if (!is_array($headers)) {
        $headers = [];
      }
    }
    catch (\Exception $e) {
      $code = $this->getExceptionCode(__METHOD__);

      throw new \Exception('An error occurred attempting to build an authenticated request.', $code);
    }
  }

  public function sendAuthenticatedRequest($url, array $headers = [], $method = 'GET', array $body = [], array $context = []) {
    $global = $this->getAuthenticatedHeaders($context);

    $headers = array_merge($global, $headers);

    $options = [
      'url' => $url,
      'headers' => $headers,
      'method' => $method,
      'body' => $body,
      'context' => $context,
    ];

    return $this->sendRequest($options);
  }

  public function processResponse($response) {
    $output = NULL;

    if ($response instanceof ResponseInterface) {
      $output = $response->getBody();
      $output = json_decode($output);
    }

    return $output;
  }

  public function getVoiceDefaults() {
    return [
      'name' => '',
      'files' => [],
      'description' => '',
      'labels' => '',
    ];
  }

  public function createVoice(array $options = []) {
    $endpoint = ElevenLabsConstants::PATH_VOICE_ADD;

    try {
      $this->getAuthenticatedRequest($endpoint, $url, $headers);

      $defaults = $this->getVoiceDefaults();

      $options = array_merge($defaults, $options);

      $keys = [
        'name',
        'description',
        'labels',
      ];

      $body = array_intersect_key($options, array_flip($keys));

      $client = $this->getClient();

      $parts = [];

      foreach ($body as $key => $value) {
        $parts[] = [
          'name' => $key,
          'contents' => $value,
        ];
      }

      $parts[] = [
        'name' => 'files',
        'contents' => file_get_contents($options['files'][0]),
        'filename' => basename($options['files'][0]),
      ];

      $authenticated_headers = $this->getAuthenticatedHeaders();

      $headers = array_merge($headers, $authenticated_headers);
      unset($headers['Content-Type']);

      $options = [
        'headers' => $headers,
        'multipart' => $parts,
      ];

      $response = $client->post($url, $options);

      $result = $response->getBody()->getContents();
      $result = json_decode($result);

      if (is_object($result) && isset($result->voice_id)) {
        $output = $result->voice_id;
      }
    }
    catch (ClientException $e) {
      $json = $e->getResponse()->getBody()->getContents();
      $json = json_decode($json);

      if (isset($json->detail->status)) {
        $status = $json->detail->status;

        switch ($status) {
          case 'can_not_use_instant_voice_cloning':
            throw new \Exception($json->detail->message, 0, $e);
        }
      }
    }
    catch (\Exception $e) {
      $message = 'An error occurred attempting to create an voice.';

      throw new \Exception($message, 0, $e);
    }

    return $output;
  }

  public function voices() {
    $output = [];

    $path = ElevenLabsConstants::PATH_VOICES;

    try {
      $this->getAuthenticatedRequest($path, $url, $headers);

      $response = $this->sendAuthenticatedRequest($url, $headers);
      $results = $this->processResponse($response);

      if (is_object($results) && isset($results->voices)) {
        $output = [];

        $items = &$results->voices;

        foreach ($items as $item) {
          $output[] = Voice::fromObject($item);
        }
      }
    } catch (\Exception $e) {
      $message = 'An error occurred retrieving the voices.';
      $code = $this->getExceptionCode(__METHOD__);

      throw new \Exception($message, $code, $e);
    }

    return $output;
  }

  public function voice($id) {
    $output = [];

    $path = ElevenLabsConstants::PATH_VOICE;

    try {
      $this->getAuthenticatedRequest($path, $url, $headers);

      $url = str_replace('{voice_id}', $id, $url);

      $response = $this->sendAuthenticatedRequest($url, $headers);
      $results = $this->processResponse($response);

      if (is_object($results) && isset($results->voice_id)) {
        $output = Voice::fromObject($results);
      }
    } catch (\Exception $e) {
      $message = 'An error occurred retrieving the voice.';
      $code = $this->getExceptionCode(__METHOD__);

      throw new \Exception($message, $code, $e);
    }

    return $output;
  }

  public function deleteVoice($id) {
    $endpoint = ElevenLabsConstants::PATH_VOICE;

    try {
      $this->getAuthenticatedRequest($endpoint, $url, $headers);

      $url = str_replace('{voice_id}', $id, $url);

      $this->sendAuthenticatedRequest($url, $headers, 'DELETE');

      return TRUE;
    } catch (\Exception $e) {
      $message = 'An error occurred deleting the voice.';

      throw new \Exception($message, 0, $e);
    }
  }

  public function getTextToSpeechDefaults() {
    return [
      'voice_id' => '',
      'text' => '',
      'voice_settings' => [
        'stability' => ElevenLabsConstants::DEFAULT_STABILITY,
        'similarity_boost' => ElevenLabsConstants::DEFAULT_SIMILARITY_BOOST,
      ],
    ];
  }

  public function textToSpeech(array $options) {
    $endpoint = ElevenLabsConstants::PATH_TEXT_TO_SPEECH;

    try {
      $this->getAuthenticatedRequest($endpoint, $url, $headers);

      $defaults = $this->getTextToSpeechDefaults();

      $options = array_merge($defaults, $options);

      $voice_id = $options['voice_id'];

      $url = str_replace('{voice_id}', $voice_id, $url);

      $keys = [
        'text',
        'voice_settings',
      ];

      $body = array_intersect_key($options, array_flip($keys));

      $output = $this->sendAuthenticatedRequest($url, $headers, 'POST', $body);
    }
    catch (ClientException $e) {
      $json = $e->getResponse()->getBody()->getContents();
      $json = json_decode($json);

      if (isset($json->detail)) {
        // @todo Implement error handling
      }
    }
    catch (\Exception $e) {
      $message = 'An error occurred attempting to convert text to speech.';

      throw new \Exception($message, 0, $e);
    }

    return $output;
  }

  public function histories() {
    $output = [];

    $path = ElevenLabsConstants::PATH_HISTORY;

    try {
      $this->getAuthenticatedRequest($path, $url, $headers);

      $response = $this->sendAuthenticatedRequest($url, $headers);
      $results = $this->processResponse($response);

      if (is_object($results) && isset($results->history)) {
        $output = [];

        $items = &$results->history;

        foreach ($items as $item) {
          $output[] = History::fromObject($item);
        }
      }
    } catch (\Exception $e) {
      $message = 'An error occurred retrieving the history.';
      $code = $this->getExceptionCode(__METHOD__);

      throw new \Exception($message, $code, $e);
    }

    return $output;
  }

  public function history($id) {
    $path = ElevenLabsConstants::PATH_AUDIO_HISTORY;

    try {
      $this->getAuthenticatedRequest($path, $url, $headers);

      $url = str_replace('{history_item_id}', $id, $url);

      return $this->sendAuthenticatedRequest($url, $headers);
    } catch (\Exception $e) {
      $message = 'An error occurred retrieving the audio for the history item.';
      $code = $this->getExceptionCode(__METHOD__);

      throw new \Exception($message, $code, $e);
    }
  }

  public function deleteHistory($id) {
    $endpoint = ElevenLabsConstants::PATH_HISTORY_ITEM;

    try {
      $this->getAuthenticatedRequest($endpoint, $url, $headers);

      $url = str_replace('{history_item_id}', $id, $url);

      $this->sendAuthenticatedRequest($url, $headers, 'DELETE');

      return TRUE;
    } catch (\Exception $e) {
      $message = 'An error occurred deleting the history.';

      throw new \Exception($message, 0, $e);
    }
  }

  public function downloadHistory(array $options) {
    $path = ElevenLabsConstants::PATH_HISTORY_DOWNLOAD;

    $defaults = [
      'ids' => [],
    ];

    $options = array_merge($defaults, $options);

    try {
      $this->getAuthenticatedRequest($path, $url, $headers);

      $body = [];

      $ids = array_values($options['ids']);

      $body['history_item_ids'] = $ids;

      return $this->sendAuthenticatedRequest($url, $headers, 'POST', $body);
    } catch (\Exception $e) {
      $message = 'An error occurred attempting to download the history items.';
      $code = $this->getExceptionCode(__METHOD__);

      throw new \Exception($message, $code, $e);
    }
  }

  public function getUserInfo(): object {
    $path = ElevenLabsConstants::PATH_USER_INFO;

    try {
      $this->getAuthenticatedRequest($path, $url, $headers);

      $response = $this->sendAuthenticatedRequest($url, $headers);
      $output = $this->processResponse($response);
    } catch (\Exception $e) {
      $message = 'An error occurred retrieving the user info.';
      $code = $this->getExceptionCode(__METHOD__);

      throw new \Exception($message, $code, $e);
    }

    return $output;
  }

  /**
   * Gets the exception code.
   *
   * @param string $input
   *   A string containing the exception code.
   *
   * @return int
   *   An integer containing the exception code.
   */
  public function getExceptionCode($input) {
    return crc32($input);
  }

}
