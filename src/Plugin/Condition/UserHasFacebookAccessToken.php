<?php

namespace Drupal\social_auth_facebook\Plugin\Condition;

use Drupal\rules\Core\RulesConditionBase;
use Drupal\social_auth_facebook\FacebookAuthPersistentDataHandler;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'User has a Facebook access token' condition.
 *
 * Check user has logged in by simple fb connect checking access token
 * from session.
 *
 * @Condition(
 *   id = "social_auth_facebook_user_has_facebook_access_token",
 *   label = @Translation("User has a Facebook access token"),
 *   category = @Translation("User")
 * )
 */
class UserHasFacebookAccessToken extends RulesConditionBase implements ContainerFactoryPluginInterface {

  /**
   * Simple FB Connect persistent data handler for accessing session data.
   *
   * @var \Drupal\simple_fb_connect\SimpleFbConnectPersistentDataHandler
   */
  protected $persistentDataHandler;

  /**
   * Constructs a UserHasFacebookAccessToken object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param FacebookAuthPersistentDataHandler $persistent_data_handler
   *   Persistent data handler of facebook.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FacebookAuthPersistentDataHandler $persistent_data_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->persistentDataHandler = $persistent_data_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('social_auth_facebook.persistent_data_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    // Check that access token is found and that is is not expired.
    if ($token = $this->persistentDataHandler->get('access_token')) {
      if ($token->isExpired !== TRUE) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
