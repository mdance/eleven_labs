<?php

namespace Drupal\eleven_labs\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Link;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\State\StateInterface;
use Drupal\eleven_labs\ElevenLabsConstants;
use Drupal\eleven_labs\ElevenLabsServiceInterface;
use Drupal\eleven_labs\ElevenLabsUtilities;
use Drupal\eleven_labs\VoiceInterface;
use Drupal\file\Entity\File;
use Drupal\file\FileStorageInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Drupal\multivalue_form_element\Element\MultiValue;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides the TextToSpeech class.
 */
class TextToSpeechForm extends FormBase implements TrustedCallbackInterface {

  /**
   * Provides the file storage.
   */
  protected FileStorageInterface $storage;

  /**
   * Provides the request.
   */
  protected Request $request;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    protected ElevenLabsServiceInterface $service,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected FileUsageInterface $fileUsage,
    protected FileSystemInterface $fileSystem,
  ) {
    $this->storage = $this->entityTypeManager->getStorage('file');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('eleven_labs'),
      $container->get('entity_type.manager'),
      $container->get('file.usage'),
      $container->get('file_system'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'text_to_speech_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, VoiceInterface $voice = NULL) {
    $file = $form_state->get('file');

    if ($file) {
      $form['file'] = [
        '#type' => 'html_tag',
        '#tag' => 'audio',
        '#attributes' => [
          'controls' => 'controls',
        ],
        'source' => [
          '#type' => 'html_tag',
          '#tag' => 'source',
          '#attributes' => [
            'src' => $file->createFileUrl(),
            'type' => $file->getMimeType(),
          ],
        ],
      ];
    }

    $form['#tree'] = TRUE;

    if ($voice) {
      $form['voice_id'] = [
        '#type' => 'value',
        '#value' => $voice,
      ];
    }
    else {
      $options = $this->service->getVoiceOptions();

      $form['voice_id'] = [
        '#type' => 'select',
        '#title' => $this->t('Voice'),
        '#required' => TRUE,
        '#options' => $options,
        '#default_value' => $this->service->getDefaultVoice(),
      ];
    }

    $form['text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Text'),
      '#required' => TRUE,
    ];

    // @todo Add file upload/voice recording transcription
    /*
    $form['files'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Voice Audio'),
      '#description' => $description,
      '#accept' => 'audio/*',
      '#pre_render' => [
        [
          $this,
          'preRenderManagedFile',
        ],
      ],
      '#upload_validators' => [
        'file_validate_extensions' => [
          'mp3 wav ogg',
        ],
      ],
      '#upload_location' => 'private://eleven-labs/voices/',
    ];

    $form['files'] = [
      '#type' => 'file',
      '#title' => $this->t('Voice Audio'),
      '#description' => $description,
      '#accept' => 'audio/*',
      '#upload_validators' => [
        'file_validate_extensions' => [
          'mp3 wav ogg',
        ],
      ],
      '#upload_location' => 'private://eleven-labs/voices/',
    ];

    $form['audio'] = [
      '#type' => 'html_tag',
      '#tag' => 'audio',
      '#attributes' => [
        'controls' => 'controls',
        'id' => 'player',
      ],
    ];

    $label_start = $this->t('Start Recording');
    $label_stop = $this->t('Stop Recording');

    $js = [
      'label_start' => $label_start,
      'label_stop' => $label_stop,
    ];

    $form['toggle'] = [
      '#type' => 'button',
      '#value' => $label_start,
    ];
    */

    $form['voice_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Voice Settings'),
    ];

    $container = &$form['voice_settings'];

    $container['stability'] = [
      '#type' => 'number',
      '#title' => $this->t('Stability'),
      '#min' => 0,
      '#default_value' => ElevenLabsConstants::DEFAULT_STABILITY,
    ];

    $container['similarity_boost'] = [
      '#type' => 'number',
      '#title' => $this->t('Similarity Boost'),
      '#min' => 0,
      '#default_value' => ElevenLabsConstants::DEFAULT_SIMILARITY_BOOST,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $actions = &$form['actions'];

    $actions['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $js = [];

    $form['#attached']['drupalSettings']['eleven_labs'] = $js;
    $form['#attached']['library'][] = 'eleven_labs/default';

    return $form;
  }

  public function preRenderManagedFile(array $element) {
    $element['upload']['#attributes']['capture'] = 'capture';

    return $element;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $values = $form_state->cleanValues()->getValues();

    $keys = [
      'voice_id',
      'text',
      'voice_settings',
    ];

    $options = array_intersect_key($values, array_flip($keys));

    try {
      $directory = 'private://eleven-labs/text-to-speech';

      $result = $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);

      if (!$result) {
        $message = $this->t('An error occurred creating the text to speech storage directory');

        $form_state->setError($form, $message);
      }
      else {
        $text = $options['text'];
        $hash = hash('sha256', $text);

        $response = $this->service->textToSpeech($options);

        $body = $response->getBody();

        $filename = $hash . '.mp3';
        $uri = $directory . '/' . $filename;

        $result = $this->fileSystem->saveData($body, $uri, FileSystemInterface::EXISTS_RENAME);

        $values = [
          'uri' => $uri,
          'filename' => $result,
        ];

        $file = File::create($values);
        $file->save();

        $this->fileUsage->add($file, 'eleven_labs', 'text_to_speech', $hash);

        $form_state->set('file', $file);
      }
    }
    catch (\Exception $e) {
      $form_state->setError($form, $e->getMessage());
    }
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

  /**
   * @inheritDoc
   */
  public static function trustedCallbacks() {
    return ['preRenderManagedFile'];
  }

}
