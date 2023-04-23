<?php

namespace Drupal\eleven_labs;

use Drupal\Core\Datetime\DrupalDateTime;

interface HistoryInterface {

  /**
   * Gets the ID.
   */
  public function getId(): string;

  /**
   * Sets the ID.
   */
  public function setId(string $id): self;

  /**
   * Gets the voice ID.
   */
  public function getVoiceId(): string;

  /**
   * Sets the voice ID.
   *
   * @param string $voiceId
   *   The voice ID.
   *
   * @return self
   */
  public function setVoiceId(string $voiceId): self;

  /**
   * Gets the voice name.
   */
  public function getVoiceName(): string;

  /**
   * Sets the voice name
   *
   * @param string $voiceName
   *   The voice name.
   *
   * @return self
   */
  public function setVoiceName(string $voiceName): self;

  /**
   * Gets the text.
   */
  public function getText(): string;

  /**
   * Sets the text.
   *
   * @param string $text
   *   The text.
   *
   * @return self
   */
  public function setText(string $text): self;

  /**
   * Gets the date.
   */
  public function getDate(): int;

  /**
   * Sets the date.
   *
   * @param int $date
   *   The date.
   *
   * @return self
   */
  public function setDate(int $date): self;

  /**
   * Gets the date object.
   */
  public function getDateObject(): DrupalDateTime;

  /**
   * Gets the character count change from.
   */
  public function getCharacterCountChangeFrom(): int;

  /**
   * Sets the character count change from.
   *
   * @param int $characterCountChangeFrom
   *   The character count change from.
   *
   * @return self
   */
  public function setCharacterCountChangeFrom(int $characterCountChangeFrom): self;

  /**
   * Gets the character count change to.
   */
  public function getCharacterCountChangeTo(): int;

  /**
   * Sets the character count change to.
   *
   * @param int $characterCountChangeTo
   *   The character count change to.
   *
   * @return self
   */
  public function setCharacterCountChangeTo(int $characterCountChangeTo): self;

  /**
   * Gets the content type.
   */
  public function getContentType(): string;

  /**
   * Sets the content type.
   *
   * @param string $contentType
   *   The content type.
   *
   * @return self
   */
  public function setContentType(string $contentType): self;

  /**
   * Gets the state.
   */
  public function getState(): string;

  /**
   * Sets the state.
   *
   * @param string $state
   *   The state.
   *
   * @return self
   */
  public function setState(string $state): self;

  /**
   * Gets the settings.
   */
  public function getSettings(): object;

  /**
   * Sets the settings.
   *
   * @param object $settings
   *   The settings.
   *
   * @return self
   */
  public function setSettings(object $settings): self;

  /**
   * Gets the renderable array.
   *
   * @return array
   *   The renderable array.
   */
  public function toRenderable(): array;

}
