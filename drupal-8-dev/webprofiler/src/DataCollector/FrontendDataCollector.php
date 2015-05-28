<?php

/**
 * @file
 * Contains \Drupal\webprofiler\DataCollector\FrontendDataCollector.
 */

namespace Drupal\webprofiler\DataCollector;

use Drupal\webprofiler\DrupalDataCollectorInterface;
use Drupal\webprofiler\Frontend\PerformanceData;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Collects data about frontend performance.
 */
class FrontendDataCollector extends DataCollector implements DrupalDataCollectorInterface {

  use StringTranslationTrait, DrupalDataCollectorTrait;

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Exception $exception = NULL) {

  }

  /**
   * @param $data
   */
  public function setData($data) {
    $this->data['performance'] = $data;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'frontend';
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->t('Frontend');
  }

  /**
   * {@inheritdoc}
   */
  public function getPanelSummary() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getPanel() {
    $build = array();

    if (!isset($this->data['performance'])) {
      $build['message'] = [
        '#type' => 'inline_template',
        '#template' => '{{ message }}',
        '#context' => [
          'message' => $this->t('No collected data, maybe this profile hasn\'t been generated by a browser o has been generated by an unsupported browser.'),
        ],
      ];

      return $build;
    }

    $build['title'] = [
      '#type' => 'inline_template',
      '#template' => '<h3>{{ message }}</h3>',
      '#context' => [
        'message' => $this->t('Timing API data'),
      ],
    ];

    $cssHeader = array(
      'metric',
      'value',
    );

    $performanceData = new PerformanceData($this->data['performance']);

    $rows = [
      [$this->t('DNS lookup time'), $performanceData->getDNSTiming() . ' ms'],
      [$this->t('TCP handshake time'), $performanceData->getTCPTiming() . ' ms'],
      [$this->t('Time to first byte'), $performanceData->getTtfbTiming() . ' ms'],
      [$this->t('Data download time'), $performanceData->getDataTiming() . ' ms'],
      [$this->t('DOM building time'), $performanceData->getDomTiming() . ' ms'],
    ];

    $build['table'] = [
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => $cssHeader,
      '#sticky' => TRUE,
    ];

    return $build;
  }
}