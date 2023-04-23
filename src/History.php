<?php

namespace Drupal\eleven_labs;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class History implements HistoryInterface {

  use StringTranslationTrait;

  /**
   * The ID.
   */
  protected string $id;

  /**
   * Provides the voice id.
   */
  protected string $voiceId;

  /**
   * Provides the voice name.
   */
  protected string $voiceName;

  /**
   * Provides the text.
   */
  protected string $text;

  /**
   * Provides the date.
   */
  protected int $date;

  /**
   * Provides the character count change from.
   */
  protected int $characterCountChangeFrom;

  /**
   * Provides the character count change to.
   */
  protected int $characterCountChangeTo;

  /**
   * Provides the content type.
   */
  protected string $contentType;

  /**
   * Provides the state.
   */
  protected string $state;

  /**
   * Provides the settings.
   */
  protected object $settings;

  public function getId(): string {
    return $this->id;
  }

  public function setId(string $id): self {
    $this->id = $id;

    return $this;
  }

  public function getVoiceId(): string {
    return $this->voiceId;
  }

  public function setVoiceId(string $voiceId): self {
    $this->voiceId = $voiceId;

    return $this;
  }

  public function getVoiceName(): string {
    return $this->voiceName;
  }

  public function setVoiceName(string $voiceName): self {
    $this->voiceName = $voiceName;

    return $this;
  }

  public function getText(): string {
    return $this->text;
  }

  public function setText(string $text): self {
    $this->text = $text;

    return $this;
  }

  public function getDate(): int {
    return $this->date;
  }

  public function setDate(int $date): self {
    $this->date = $date;

    return $this;
  }

  public function getDateObject(): DrupalDateTime {
    return DrupalDateTime::createFromTimestamp($this->date);
  }

  public function getCharacterCountChangeFrom(): int {
    return $this->characterCountChangeFrom;
  }

  public function setCharacterCountChangeFrom(int $characterCountChangeFrom): self {
    $this->characterCountChangeFrom = $characterCountChangeFrom;

    return $this;
  }

  public function getCharacterCountChangeTo(): int {
    return $this->characterCountChangeTo;
  }

  public function setCharacterCountChangeTo(int $characterCountChangeTo): self {
    $this->characterCountChangeTo = $characterCountChangeTo;

    return $this;
  }

  public function getContentType(): string {
    return $this->contentType;
  }

  public function setContentType(string $contentType): self {
    $this->contentType = $contentType;

    return $this;
  }

  public function getState(): string {
    return $this->state;
  }

  public function setState(string $state): self {
    $this->state = $state;

    return $this;
  }

  public function getSettings(): object {
    return $this->settings;
  }

  public function setSettings(object $settings): self {
    $this->settings = $settings;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public static function fromObject(object $data): self {
    $output = new History();

    $mappings = [
      'id' => 'history_item_id',
      'voiceId' => 'voice_id',
      'voiceName' => 'voice_name',
      'text' => 'text',
      'date' => 'date_unix',
      'characterCountChangeFrom' => 'character_count_change_from',
      'characterCountChangeTo' => 'character_count_change_to',
      'contentType' => 'content_type',
      'state' => 'state',
      'settings' => 'settings',
    ];

    $results = ElevenLabsUtilities::processMappings($data, $mappings);

    foreach ($results as $k => $v) {
      $output->$k = $v;
    }

    return $output;
  }

  public function toRenderable(): array {
    $output = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'history',
          'history-state-' . $this->getState(),
        ],
      ],
    ];

    $output['id'] = [
      '#type' => 'item',
      '#title' => $this->t('ID'),
      '#markup' => $this->getId(),
      '#attributes' => [
        'class' => [
          'history-id',
        ],
      ],
    ];

    return $output;
  }

}
