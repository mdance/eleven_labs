<?php

namespace Drupal\eleven_labs\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\eleven_labs\ElevenLabsServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides the HistoryForm class.
 */
class HistoryForm extends FormBase {

  /**
   * {@inheritDoc}
   */
  public function __construct(
    protected ElevenLabsServiceInterface $service
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('eleven_labs')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'eleven_labs_history_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $items = $this->service->histories();

    $header = [];

    $header['id'] = $this->t('ID');
    $header['voice'] = $this->t('Voice');
    $header['text'] = $this->t('Text');
    $header['date'] = $this->t('Date');
    $header['change'] = $this->t('Character Count Change');
    $header['content_type'] = $this->t('Content Type');
    $header['state'] = $this->t('State');
    $header['operations'] = $this->t('Operations');

    $options = [];

    foreach ($items as $item) {
      $option = [];

      $id = $item->getId();

      $route_parameters = [];

      $route_parameters['voice'] = $item->getVoiceId();

      $link = Link::createFromRoute($item->getVoiceName(), 'eleven_labs.voice.view', $route_parameters);

      $option['id'] = $id;
      $option['voice'] = $link;
      $option['text'] = $item->getText();
      $option['date'] = $item->getDateObject()->format('Y-m-d');
      $option['change'] = $item->getCharacterCountChangeFrom() . ':' . $item->getCharacterCountChangeTo();
      $option['content_type'] = $item->getContentType();
      $option['state'] = ucwords($item->getState());

      $operations = [];

      $route_parameters = [];

      $route_parameters['history'] = $id;

      $url = Url::fromRoute('eleven_labs.history.view', $route_parameters);

      $operations['view'] = [
        'title' => $this->t('View'),
        'url' => $url,
      ];

      $url = Url::fromRoute('eleven_labs.history.download', $route_parameters);

      $operations['download'] = [
        'title' => $this->t('Download'),
        'url' => $url,
      ];

      $url = Url::fromRoute('eleven_labs.history.delete', $route_parameters);

      $operations['delete'] = [
        'title' => $this->t('Delete'),
        'url' => $url,
      ];

      $option['operations'] = [
        'data' => [
          '#type' => 'operations',
          '#links' => $operations,
        ],
      ];

      $options[$id] = $option;
    }

    $form['items'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $options,
      '#empty' => $this->t('There is no history available at this time.'),
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $actions = &$form['actions'];

    $actions['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Download'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->cleanValues()->getValues();
    $items = $values['items'];
    $items = array_filter($items);
    $total = count($items);

    $filename = 'history.zip';

    if ($total === 1) {
      $filename = array_values($items)[0] . '.mp3';
    }

    $options = [
      'ids' => $items,
    ];

    $result = $this->service->downloadHistory($options);

    $content = $result->getBody();
    $status = $result->getStatusCode();
    $headers = $result->getHeaders();

    $headers['Content-Disposition'] = "attachment; filename=\"$filename\"";

    $response = new Response($content, $status, $headers);

    $form_state->setResponse($response);
  }

}
