<?php

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/**
 * Shared admin concerns: asset enqueuing and plugin row links.
 */
class Admin
{
    private Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);

        add_filter('plugin_action_links_' . $this->plugin->basename, [$this, 'addActionLinks']);
        add_filter('plugin_row_meta', [$this, 'addRowMeta'], 10, 2);
    }

    /**
     * Load admin assets on our own screens only. Our pages' hook suffixes
     * always contain the slug (tools_page_{slug}, toplevel_page_{slug}, ...),
     * whatever menu they live under.
     */
    public function enqueueAssets(string $hook): void
    {
        if (! str_contains($hook, Plugin::SLUG)) {
            return;
        }

        [$cssUrl, $cssVer] = $this->plugin->asset('assets/css/admin.css');
        [$jsUrl, $jsVer]   = $this->plugin->asset('assets/js/admin.js');

        wp_enqueue_style(Plugin::SLUG . '-admin', $cssUrl, [], $cssVer);

        wp_enqueue_script(Plugin::SLUG . '-admin', $jsUrl, [], $jsVer, ['in_footer' => true]);

        wp_localize_script(Plugin::SLUG . '-admin', Plugin::JS . 'Admin', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'action'  => Ajax::ACTION_PING,
            'nonce'   => wp_create_nonce(Ajax::NONCE),
            'i18n'    => [
                'pinging' => __('Pinging…', 'clio-base-plugin'),
                'failed'  => __('Request failed.', 'clio-base-plugin'),
            ],
        ]);
    }

    /**
     * "Settings" link first in our row's action links (next to Deactivate).
     */
    public function addActionLinks(array $actions): array
    {
        array_unshift($actions, sprintf(
            '<a href="%s">%s</a>',
            esc_url($this->plugin->settingsUrl()),
            esc_html__('Settings', 'clio-base-plugin')
        ));

        return $actions;
    }

    /**
     * Extra links in our row's meta line (next to "Version x | By ...").
     */
    public function addRowMeta(array $meta, string $pluginFile): array
    {
        if ($pluginFile !== $this->plugin->basename) {
            return $meta;
        }

        $meta[] = sprintf(
            '<a href="%s">%s</a>',
            esc_url($this->plugin->settingsUrl(['tab' => 'about'])),
            esc_html__('About', 'clio-base-plugin')
        );

        $meta[] = sprintf(
            '<a href="%s" target="_blank" rel="noopener">%s</a>',
            esc_url('https://cliowebsites.com'),
            esc_html__('Support', 'clio-base-plugin')
        );

        return $meta;
    }
}
