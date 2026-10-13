<?php
/**
 * Runs on "Delete" from the Plugins screen. The plugin is NOT booted here —
 * we only load Plugin.php for its ID constants.
 */

namespace Clio\BasePlugin;

defined('WP_UNINSTALL_PLUGIN') || exit;

require_once __DIR__ . '/includes/Plugin.php';

$options = get_option(Plugin::OPTION, []);

if (empty($options['delete_data_on_uninstall'])) {
    return;
}

delete_option(Plugin::OPTION);
delete_option(Plugin::DB_VERSION);
