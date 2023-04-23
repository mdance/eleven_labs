<?php

namespace Drupal\eleven_labs;

use Drupal\Core\StringTranslation\StringTranslationTrait;

class Voice implements VoiceInterface {

  use StringTranslationTrait;

  /**
   * The voice ID.
   */
  protected string $id;

  /**
   * The name of the voice.
   */
  protected string $name;

  /**
   * The samples of the voice.
   */
  protected ?array $samples;

  /**
   * The category of the voice.
   */
  protected string $category;

  /**
   * Fine-tuning information for the voice.
   */
  protected object $fineTuning;

  /**
   * Labels for the voice.
   */
  protected object $labels;

  /**
   * Description of the voice.
   */
  protected string $description = '';

  /**
   * URL for previewing the voice.
   */
  protected string $previewUrl;

  /**
   * Available tiers for the voice.
   */
  protected array $availableTiers;

  /**
   * Settings for the voice.
   */
  protected ?array $settings;

  public function getId(): string {
    return $this->id;
  }

  public function setId(string $id): self {
    $this->id = $id;

    return $this;
  }

  public function getName(): string {
    return $this->name;
  }

  public function setName(string $name): self {
    $this->name = $name;
    return $this;
  }

  public function getSamples(): ?object {
    return $this->samples;
  }

  public function setSamples(?object $samples): self {
    $this->samples = $samples;
    return $this;
  }

  public function getCategory(): string {
    return $this->category;
  }

  public function setCategory(string $category): self {
    $this->category = $category;
    return $this;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function setDescription(string $description): self {
    $this->description = $description;

    return $this;
  }

  public function getLabels(): object {
    return $this->labels;
  }

  public function setLabels(object $labels): self {
    $this->labels = $labels;

    return $this;
  }

  public function setFineTuning(object $fine_tuning): object {
    $this->fineTuning = $fine_tuning;

    return $this;
  }

  public function getFineTuning(): object {
    return $this->fineTuning;
  }

  public static function fromObject(object $data) {
    $output = new Voice();

    $mappings = [
      'id' => 'voice_id',
      'name' => 'name',
      'samples' => 'samples',
      'category' => 'category',
      'fineTuning' => 'fine_tuning',
      'labels' => 'labels',
      'description' => 'description',
      'previewUrl' => 'preview_url',
      'availableTiers' => 'available_tiers',
      'settings' => 'settings',
    ];

    $results = ElevenLabsUtilities::processMappings($data, $mappings);

    foreach ($results as $k => $v) {
      $output->$k = $v;
    }

    return $output;
  }

  public function toRenderable(): array {
    $category = $this->getCategory();

    $output = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'voice',
          'voice-category-' . $category,
        ],
      ],
    ];

    $output['id'] = [
      '#type' => 'item',
      '#title' => $this->t('ID'),
      '#markup' => $this->getId(),
      '#attributes' => [
        'class' => [
          'voice-id',
        ],
      ],
    ];

    $output['name'] = [
      '#type' => 'item',
      '#title' => $this->t('Name'),
      '#markup' => $this->getName(),
      '#attributes' => [
        'class' => [
          'voice-name',
        ],
      ],
    ];

    $output['category'] = [
      '#type' => 'item',
      '#title' => $this->t('Category'),
      '#markup' => $this->getCategory(),
      '#attributes' => [
        'class' => [
          'voice-category',
        ],
      ],
    ];

    $result = $this->getDescription();

    if (!empty($result)) {
      $output['description'] = [
        '#type' => 'item',
        '#title' => $this->t('Description'),
        '#markup' => $result,
        '#attributes' => [
          'class' => [
            'voice-description',
          ],
        ],
      ];
    }

    return $output;
  }

}
