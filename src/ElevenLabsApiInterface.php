<?php

namespace Drupal\eleven_labs;


/**
 * Provides the ElevenLabsApi class.
 */
interface ElevenLabsApiInterface {

  /**
   * {@inheritDoc}
   */
  public function getDefaultConfiguration();

  /**
   * {@inheritDoc}
   */
  public function getConfiguration();

  /**
   * {@inheritDoc}
   */
  public function setConfiguration($input = []);

  /**
   * {@inheritDoc}
   */
  public function getUrl();

  /**
   * {@inheritDoc}
   */
  public function getApiKey();

  /**
   * {@inheritDoc}
   */
  public function getClient();

  /**
   * {@inheritDoc}
   */
  public function getUserAgent(array $context = []);

  /**
   * {@inheritDoc}
   */
  public function getContentType(array $context = []);

  /**
   * {@inheritDoc}
   */
  public function getAccept(array $context = []);

  /**
   * {@inheritDoc}
   */
  public function getContentLength(array $context = []);

  /**
   * {@inheritDoc}
   */
  public function getAcceptEncoding(array $context = []);

  /**
   * {@inheritDoc}
   */
  public function getHeaderMap(array $context = []);

  /**
   * {@inheritDoc}
   */
  public function getHeaders(array $context = []);

  /**
   * {@inheritDoc}
   */
  public function getAuthenticatedHeaders(array $context = []);

  /**
   * Sends an HTTP request.
   *
   * @param array $options
   *   Provides an array of request options:
   *     url: A string containing the request url.
   *     method: A string containing the request method.
   *     headers: An array containing request headers.
   *     body: The POST body.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Returns the response object.
   */
  public function sendRequest(array $options = []);

  /**
   * {@inheritDoc}
   */
  public function sendHttpRequest($url, array $headers, string $method = 'GET', array $body = []);

  /**
   * Processes the body.
   *
   * @param mixed $body
   *   Provides the body.
   *
   * @return mixed
   *   The processed body.
   */
  public function processBody($body);

  /**
   * Processes the url, and headers for a authenticated request.
   *
   * @param string $endpoint
   *   A string containing the endpoint.
   * @param string $url
   *   A string passed by reference that will contain the authenticated url.
   * @param mixed $headers
   *   An array passed by reference that will contain the request headers.
   * @param mixed $query
   *   An optiohnal string or array of query string parameters.
   */
  public function getAuthenticatedRequest($endpoint, &$url, &$headers = [], $query = NULL);

  /**
   * Sends an authenticated request.
   *
   * @param string $url
   *   A string containing the request URL.
   * @param array $headers
   *   An array of request headers.
   * @param string $method
   *   A string containing the HTTP request method.
   * @param array $body
   *   Optional POST parameters.
   * @param array $context
   *   An array of context variables for processing.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Returns the response object.
   */
  public function sendAuthenticatedRequest($url, array $headers = [], $method = 'GET', array $body = [], array $context = []);

  /**
   * Processes a response.
   *
   * @param mixed $response
   *   The response data.
   *
   * @return mixed
   *   Returns the processed response.
   */
  public function processResponse($response);

  /**
   * {@inheritDoc}
   */
  public function createVoice(array $options = []);

  /**
   * {@inheritDoc}
   */
  public function voices();

  /**
   * {@inheritDoc}
   */
  public function voice($id);

  /**
   * {@inheritDoc}
   */
  public function deleteVoice($id);

  /**
   * {@inheritDoc}
   */
  public function histories();

  /**
   * {@inheritDoc}
   */
  public function history($id);

  /**
   * {@inheritDoc}
   */
  public function deleteHistory($id);

  /**
   * Gets the user info.
   *
   * @return object
   *   The user info
   */
  public function getUserInfo(): object;

}
