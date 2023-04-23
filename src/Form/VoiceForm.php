<?php

namespace Drupal\eleven_labs\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Link;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\State\StateInterface;
use Drupal\eleven_labs\ElevenLabsConstants;
use Drupal\eleven_labs\ElevenLabsServiceInterface;
use Drupal\eleven_labs\ElevenLabsUtilities;
use Drupal\eleven_labs\VoiceInterface;
use Drupal\file\FileStorageInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Drupal\multivalue_form_element\Element\MultiValue;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides the VoiceForm class.
 */
class VoiceForm extends FormBase implements TrustedCallbackInterface {

  public const MODE_NEW = 'new';

  public const MODE_EDIT = 'edit';

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
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'eleven_labs_voice_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, VoiceInterface $voice = NULL) {
    $mode = self::MODE_NEW;

    if ($voice) {
      $mode = self::MODE_EDIT;
    }

    $form['item'] = [
      '#type' => 'value',
      '#value' => $voice,
    ];

    if ($mode === self::MODE_EDIT) {
      $form['id'] = [
        '#type' => 'item',
        '#title' => $this->t('ID'),
        '#markup' => $voice->getId(),
      ];
    }

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $voice ? $voice->getName() : '',
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $voice ? $voice->getDescription() : '',
    ];

    $description = $this->t('Please specify the custom voice audio recording, this will be used to train the custom voice.');

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
    */

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

    $default_value = '';

    if ($voice) {
      $results = $voice->getLabels();

      if ($results) {

      }
    }

    $form['labels'] = [
      '#type' => 'multivalue',
      '#title' => $this->t('Labels'),
      '#cardinality' => MultiValue::CARDINALITY_UNLIMITED,
      'label' => [
        '#type' => 'textfield',
        '#title' => $this->t('Label'),
      ],
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $actions = &$form['actions'];

    $actions['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

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

    $files = $this->getRequest()->files->get('files', []);

    if (isset($files['files'])) {
      $file = $files['files'] ?? NULL;

      if ($file) {
        $path = (string)$file;

        if (!empty($path)) {
          $labels = [];

          foreach ($values['labels'] as $label) {
            if (empty($label['label'])) {
              continue;
            }

            $labels[] = $label['label'];
          }

          $total = count($labels);

          if ($total) {
            $labels = json_encode($labels);
          }
          else {
            $labels = '';
          }

          $options = [
            'name' => $values['name'],
            'files' => [
              $path,
            ],
            'description' => $values['description'],
            'labels' => $labels,
          ];

          try {
            $id = $this->service->createVoice($options);

            $form_state->set('voice_id', $id);
          } catch (\Exception $e) {
            $form_state->setError($form, $e->getMessage());
          }
        }
      }
    }

    /*
    $fids = $values['files'];

    foreach ($fids as $fid) {
      $file = $this->storage->load($fid);

      $this->fileUsage->add($file, 'eleven_labs', 'voice', $fid);
    }
    */
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * @inheritDoc
   */
  public static function trustedCallbacks() {
    return ['preRenderManagedFile'];
  }

}
