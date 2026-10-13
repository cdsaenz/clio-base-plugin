<?php

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/**
 * Front-end: assets are registered globally but only enqueued when
 * something on the page actually needs them (here: the shortcode).
 *
 * Usage: [clio_base_plugin]  (Frontend::SHORTCODE)
 */
class Frontend
{
    public const SHORTCODE = Plugin::PREFIX;

    private Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
        add_shortcode(self::SHORTCODE, [$this, 'renderShortcode']);
    }

    public function registerAssets(): void
    {
        [$cssUrl, $cssVer] = $this->plugin->asset('assets/css/frontend.css');
        [$jsUrl, $jsVer]   = $this->plugin->asset('assets/js/frontend.js');

        wp_register_style(Plugin::SLUG, $cssUrl, [], $cssVer);
        wp_register_script(Plugin::SLUG, $jsUrl, [], $jsVer, ['in_footer' => true, 'strategy' => 'defer']);

        wp_localize_script(Plugin::SLUG, Plugin::JS, [
            'i18n' => [
                'show' => __('Show details', 'clio-base-plugin'),
                'hide' => __('Hide details', 'clio-base-plugin'),
            ],
        ]);
    }

    public function renderShortcode(array|string $atts = []): string
    {
        if (! $this->plugin->option('frontend_enabled')) {
            return '';
        }

        $atts = shortcode_atts([
            'greeting' => $this->plugin->option('greeting'),
        ], $atts, self::SHORTCODE);

        wp_enqueue_style(Plugin::SLUG);
        wp_enqueue_script(Plugin::SLUG);

        return Plugin::view('shortcode', [
            'greeting' => $atts['greeting'],
        ], true);
    }
}
