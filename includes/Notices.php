<?php

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/**
 * Flash notices that survive a redirect.
 *
 * Notices::add('Done.');                 // success
 * Notices::add('Careful.', 'warning');   // success|info|warning|error
 *
 * Stored per user in a short-lived transient, shown once on the next admin page.
 */
class Notices
{
    private const TRANSIENT = Plugin::PREFIX . '_notices_';

    public function register(): void
    {
        add_action('admin_notices', [$this, 'render']);
    }

    public static function add(string $message, string $type = 'success'): void
    {
        $key     = self::key();
        $notices = get_transient($key) ?: [];

        $notices[] = ['type' => $type, 'message' => $message];

        set_transient($key, $notices, 5 * MINUTE_IN_SECONDS);
    }

    public function render(): void
    {
        $key     = self::key();
        $notices = get_transient($key);

        if (! $notices) {
            return;
        }

        delete_transient($key);

        foreach ($notices as $notice) {
            printf(
                '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
                esc_attr($notice['type']),
                wp_kses($notice['message'], ['strong' => [], 'em' => [], 'code' => [], 'a' => ['href' => []]])
            );
        }
    }

    private static function key(): string
    {
        return self::TRANSIENT . get_current_user_id();
    }
}
