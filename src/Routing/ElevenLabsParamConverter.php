<?php

namespace Drupal\eleven_labs\Routing;

use Drupal\Core\ParamConverter\ParamConverterInterface;
use Drupal\eleven_labs\ElevenLabsServiceInterface;
use Symfony\Component\Routing\Route;

/**
 * Provides the ElevenLabsParamConverter class.
 */
class ElevenLabsParamConverter implements ParamConverterInterface {

  /**
   * {@inheritDoc}
   */
  public function __construct(
    protected ElevenLabsServiceInterface $service
  ) {
  }

  public function map() {
    $output = [
      'eleven_labs_voice' => 'voice',
      'eleven_labs_history' => 'history',
    ];

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function convert($value, $definition, $name, array $defaults) {
    $output = NULL;

    if (!empty($value)) {
      try {
        $type = $definition['type'];
        $map = $this->map();
        $method = $map[$type];

        $output = $this->service->$method($value);
      } catch (\Exception $e) {}
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function applies($definition, $name, Route $route) {
    $output = FALSE;

    $map = $this->map();

    if (isset($definition['type'])) {
      $output = key_exists($definition['type'], $map);
    }

    return $output;
  }

}
