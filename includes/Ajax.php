<?php

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/**
 * admin-ajax endpoints. Every handler: nonce + capability check, JSON response.
 */
class Ajax
{
    public const ACTION_PING = Plugin::PREFIX . '_ping';
    public const NONCE       = Plugin::PREFIX . '_ajax';

    private Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function register(): void
    {
        add_action('wp_ajax_' . self::ACTION_PING, [$this, 'ping']);
    }

    public function ping(): void
    {
        check_ajax_referer(self::NONCE, 'nonce');

        if (! current_user_can(Plugin::CAP)) {
            wp_send_json_error(['message' => __('Unauthorized.', 'clio-base-plugin')], 403);
        }

        wp_send_json_success([
            'message' => sprintf(
                /* translators: 1: plugin version, 2: server time */
                __('Pong — v%1$s, server time %2$s', 'clio-base-plugin'),
                $this->plugin->version,
                wp_date('H:i:s')
            ),
        ]);
    }
}
