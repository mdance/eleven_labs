<?php

namespace Drupal\eleven_labs\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Link;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;
use Drupal\eleven_labs\ElevenLabsConstants;
use Drupal\eleven_labs\ElevenLabsServiceInterface;
use Drupal\eleven_labs\ElevenLabsUtilities;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the VoicesForm class.
 */
class VoicesForm extends FormBase {

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
    return 'eleven_labs_voices_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?Request $request = NULL) {
    $items = $this->service->voices();

    $header = [];

    $header['id'] = $this->t('ID');
    $header['name'] = $this->t('Name');
    $header['category'] = $this->t('Category');
    $header['operations'] = $this->t('Operations');

    $options = [];

    foreach ($items as $item) {
      $option = [];

      $id = $item->getId();

      $route_parameters = [];

      $route_parameters['voice'] = $id;

      $link = Link::createFromRoute($id, 'eleven_labs.voice.view', $route_parameters);

      $option['id'] = $link;
      $option['name'] = $item->getName();
      $option['category'] = $item->getCategory();

      $operations = [];

      $route_parameters = [];

      $route_parameters['voice'] = $id;

      $url = Url::fromRoute('eleven_labs.voice.view', $route_parameters);

      $operations['view'] = [
        'title' => $this->t('View'),
        'url' => $url,
      ];

      $url = Url::fromRoute('eleven_labs.voice.edit', $route_parameters);

      $operations['edit'] = [
        'title' => $this->t('Edit'),
        'url' => $url,
      ];

      $url = Url::fromRoute('eleven_labs.voice.delete', $route_parameters);

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
      '#empty' => $this->t('There are no voices available at this time.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->cleanValues()->getValues();
  }

}
