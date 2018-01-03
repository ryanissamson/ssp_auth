<?php

namespace Drupal\ssp_auth;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SimpleSAML_Configuration;

/**
 * Class SSPAuth.
 *
 * @package Drupal\ssp_auth
 */
class SSPAuth {

  /**
   * The configFactory interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The SSPAuth config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * The Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger interface.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;


  /**
   * Constructs a \Drupal\ssp_auth\SSPAuth object.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger interface.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory interface.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(LoggerInterface $logger, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->logger = $logger;
    $this->configFactory = $config_factory;
    $this->config = $this->configFactory->get('ssp_auth.settings');
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory')->get('ssp_auth'),
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns the SimpleSAMLphp configuration.
   *
   * @return array
   *   The SimpleSAMLphp configuration.
   */
   public function getSSPConfig() {
    // Get the simplesamlphp session.
    $basedir = $this->config->get('ssp_auth_installdir');

    if (!strlen($basedir)) {
      return [];
    }

    require_once($basedir . '/lib/_autoload.php');

    $saml_config = SimpleSAML_Configuration::getInstance();

    if (!is_object($saml_config)) {
      return [];
    }

    // get the secretsalt
    $ssp_config['secretsalt'] = $saml_config->getValue('secretsalt');

    // get the baseurlpath
    $ssp_config['baseurlpath'] = '/' . $saml_config->getValue('baseurlpath');

    unset($ssp_config);

    $saml_authsources = SimpleSAML_Configuration::getConfig('authsources.php');

    // get the cookie_name
    $ssp_config['cookie_name'] = $saml_authsources->getValue('cookie_name', 'ssp_auth');

    unset($saml_authsources);

    // Ensure that every configuration setting is present.
    foreach ($ssp_config as $val) {

      if (!strlen($val)) {
        return [];
      }

    }

    return $ssp_config;

  }

  /**
   * Sets a cookie to log the user into SAML.
   *
   * @param $uid
   * @param $username
   */
  public function setCookie($uid, $username) {
    // Get the configuration information from SimpleSAMLphp.
    $ssp_config = $this->getSSPConfig();

    // If we don't have configuration, exit.
    if (empty($ssp_config)) {
      // Log a message if configuration is not found.
      $this->logger->notice('Could not use drupalauth for %name, could not get the SimpleSAMLphp configuration.', ['%name' => $username]);
      return;
    }

    // Create a validation hash to ensure nobody tampers with the uid and store
    // the authenticated user's UID in the cookie.
    setcookie($ssp_config['cookie_name'], sha1($ssp_config['secretsalt'] . $uid) . ':' . $uid, 0, $ssp_config['baseurlpath']);


    // If the ReturnTo URL is present, send the user to the URL.
    if (isset($_GET['ReturnTo']) && $_GET['ReturnTo']) {
      header('Location: ' . $_GET['ReturnTo']);
      die;
    }
  }

}