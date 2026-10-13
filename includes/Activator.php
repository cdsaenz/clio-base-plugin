<?php

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/**
 * Activation / deactivation / install routines.
 * Uninstall cleanup lives in uninstall.php (runs without the plugin booted).
 */
class Activator
{
    public static function activate(): void
    {
        self::install();

        Notices::add(sprintf(
            /* translators: %s: settings page URL */
            __('<strong>Clio Base Plugin</strong> activated. <a href="%s">Go to settings</a>.', 'clio-base-plugin'),
            esc_url(Plugin::getInstance()->settingsUrl())
        ));
    }

    public static function deactivate(): void
    {
        // Clear scheduled events, flush rewrite rules, etc. Keep data — that's uninstall's job.
    }

    /**
     * Idempotent: safe to run on every activation and on version change.
     * Seeds defaults without overwriting existing values.
     */
    public static function install(): void
    {
        $current = get_option(Plugin::OPTION, []);
        update_option(Plugin::OPTION, array_merge(Settings::DEFAULTS, is_array($current) ? $current : []));

        update_option(Plugin::DB_VERSION, Plugin::getInstance()->version);
    }
}
