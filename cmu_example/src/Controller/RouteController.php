<?php

namespace Drupal\cmu_example\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\facets\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for AHA Status module routes.
 */
class RouteController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var Connection
   */
  private $connection;

  /**
   * Inject services needed by RouteController.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Returns a raw HTTP response of either "OK" or an HTTP 500 error.
   */
  public function content() {
    try {
      $result = $this->connection->query('SELECT COUNT(*) FROM {node}');
    }
    catch (Exception $e) {
      $result = FALSE;
    }
    if ($result) {
      return new Response('OK', 200);
    }
    else {
      return new Response('Error: Could not connect to database.', 500);
    }
  }

}
