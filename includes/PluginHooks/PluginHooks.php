<?php
declare(strict_types=1);
namespace TdNewsletter\PluginHooks;

use DI\Container;

class PluginHooks
{
  private string $pluginFilePath;
  private array $activationCallbacks = [];
  private array $deactivationCallbacks = [];
  private array $uninstallCallbacks = [];

  public function __construct(Container $c)
  {
    $this->pluginFilePath = $c->get('plugin.filepath');
  }

  public function addToActivate(callable $callback): void
  {
    $this->activationCallbacks[] = $callback;
  }

  public function addToDeactivate(callable $callback): void
  {
    $this->deactivationCallbacks[] = $callback;
  }

  public function addToUninstall(callable $callback): void
  {
    $this->uninstallCallbacks[] = $callback;
  }
  public function registerActivationHook(): self
  {

    register_activation_hook($this->pluginFilePath, function () {
      if (!current_user_can('activate_plugins'))
        return;
      $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
      check_admin_referer("activate-plugin_{$plugin}");
      // var_dump($this->activationCallbacks);wp_die();
      foreach ($this->activationCallbacks as $callback) {
        $callback();
      }
      flush_rewrite_rules();
    });
    return $this;
  }

  public function registerDeactivationHook(): self
  {
    register_deactivation_hook($this->pluginFilePath, function () {
      if (!current_user_can('activate_plugins'))
        return;
      $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
      check_admin_referer("deactivate-plugin_{$plugin}");

      foreach ($this->deactivationCallbacks as $callback) {
        $callback();
      }
      flush_rewrite_rules();
    });
    return $this;
  }

  // public static function registerUninstallHook()
  // {
  //   register_uninstall_hook($this->pluginFilePath, function () {
  //     if (!current_user_can('activate_plugins'))
  //       return;
  //     check_admin_referer('bulk-plugins');

  //     if (__FILE__ != WP_UNINSTALL_PLUGIN)
  //       return;

  //     foreach ($this->uninstallCallbacks as $callback) {
  //       $callback();
  //     }
  //     flush_rewrite_rules();
  //   });
  // }
}