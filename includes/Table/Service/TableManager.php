<?php

declare(strict_types=1);

namespace TdNewsletter\Table\Service;

use TdNewsletter\PluginHooks\PluginHooks;
use TdNewsletter\Table\Model\Table;

class TableManager {
  /**
   * @var Table[]
   */
  private array $tables = [];

  private PluginHooks $pluginHooks;

  public function __construct(PluginHooks $pluginHooks) {
    $this->pluginHooks = $pluginHooks;
  }

  public function addTable(Table $table): self {
    $this->tables[] = $table;
    return $this;
  }

  public function register(): self {

    if (!empty($this->tables)) {
      $this->pluginHooks->addToActivate(function () {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach ($this->tables as $table) {
          if ($wpdb->get_var("SHOW TABLES LIKE " . "'" . $wpdb->prefix . $table->getName() . "'") != $wpdb->prefix . $table->getName()) {
            dbDelta($table->getCreateSql());
          }
        }
      });
      $this->pluginHooks->addToDeactivate(function () {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        // dropping of the table should be in reverse order
        // for ($i = count($this->tables) - 1; $i >= 0; $i--) {
        //   $wpdb->query($this->tables[$i]->getDropSql());
        // }
      });
    }

    return $this;
  }
}
