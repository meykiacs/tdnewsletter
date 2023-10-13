<?php
declare(strict_types=1);
namespace TdNewsletter\Entity\Service;

use TdNewsletter\Entity\Model\User;
use TdNewsletter\PluginHooks\PluginHooks;

class UserManager
{

  private PluginHooks $pluginHooks;

  public function __construct(PluginHooks $pluginHooks) {
    $this->pluginHooks = $pluginHooks;
  }

  /**
   * Saves a user.
   *
   * @param User $user The user object to be saved.
   * @return int The id of the saved user.
   * @throws \Exception If there is an error updating or inserting the user.
   */
  public function save(User $user): int
  {
    if ($user->id !== 0) {
      $userID = wp_update_user($user->wp_user);
      if (is_wp_error($userID)) {
        throw new \Exception("Error Updating User");
      }
      update_user_meta($userID, 'is_activated', intval($user->is_active));
      return $userID;
    }
    $userID = wp_insert_user($user->wp_user);
    
    if (is_wp_error($userID)) {
      throw new \Exception("Error Inserting User");
    }
    update_user_meta($userID, 'is_activated', intval($user->is_active));
    $user->wp_user->ID = $userID;
    $user->id = $userID;
    return $userID;
  }

  /**
   * Retrieves a user by their ID.
   *
   * @param int $id The id of the user to retrieve.
   * @return User|null The retrieved user object, or null if the user doesn't exist.
   * @throws \Exception when could not get a valid value for is_active
   */
  public function getById(int $id): ?User
  {
    $wp_user = get_user_by('ID', $id);
    if (!$wp_user) {
      return null;
    }

    $user = new User($wp_user);
    $user_meta_active =  get_user_meta($user->id, 'is_activated', true);
    if (is_string($user_meta_active) && !empty($user_meta_active)) {
      $user->is_active = boolval($user_meta_active);
    } else {
      throw new \Exception("Error Parsing user meta activated", 1);
      
    }
    return $user;
  }

  /**
   * Deletes a user.
   *
   * @param User $user The user object to be deleted.
   * @return bool True if the user is successfully deleted.
   * @throws \Exception If there is an error deleting the user.
   */
  public function delete(User $user): bool
  {
    $result = wp_delete_user($user->id);
    if ($result === true) {
      return true;
    }
    throw new \Exception("Error Deleting the User");
  }

  public function fixActiveAdminMeta() {
    $this->pluginHooks->addToActivate(function () {
      $args = array(
        'role' => 'administrator',
        'fields' => 'ID'
    );
      $user_ids = get_users($args);
      foreach ($user_ids as $user_id) {
      update_user_meta($user_id, 'is_activated', '1');
      }
    });
  }
}