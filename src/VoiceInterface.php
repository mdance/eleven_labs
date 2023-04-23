<?php

namespace Drupal\eleven_labs;

interface VoiceInterface {

  /**
   * Gets the voice ID.
   *
   * @return string
   *   The voice ID.
   */
  public function getId(): string;

  /**
   * Sets the ID.
   *
   * @param string $id
   *   The ID.
   *
   * @return $this
   */
  public function setId(string $id): \Drupal\eleven_labs\Voice;

  /**
   * Gets the name of the voice.
   *
   * @return string
   *   The name of the voice.
   */
  public function getName(): string;

  /**
   * Sets the name of the voice.
   *
   * @param string $name
   *   The name of the voice.
   *
   * @return $this
   */
  public function setName(string $name): \Drupal\eleven_labs\Voice;

  /**
   * Gets the samples of the voice.
   *
   * @return object|null
   *   The samples of the voice, or null if none are set.
   */
  public function getSamples(): ?object;

  /**
   * Sets the samples of the voice.
   *
   * @param object|null $samples
   *   The samples of the voice, or null if none are set.
   *
   * @return $this
   */
  public function setSamples(?object $samples): \Drupal\eleven_labs\Voice;

  /**
   * Gets the category of the voice.
   *
   * @return string
   *   The category of the voice.
   */
  public function getCategory(): string;

  /**
   * Sets the category of the voice.
   *
   * @param string $category
   *   The category of the voice.
   *
   * @return $this
   */
  public function setCategory(string $category): \Drupal\eleven_labs\Voice;

  /**
   * Gets the fine-tuning information for the voice.
   *
   * @return object
   *   The fine-tuning information for the voice.
   */
  public function getFineTuning(): object;

}
