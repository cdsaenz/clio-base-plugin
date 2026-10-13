<?php

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/**
 * Manages the plugin's single option row and the Settings admin page.
 *
 * Option key: Plugin::OPTION  (array)
 *
 *   greeting                  string  text shown by the shortcode
 *   frontend_enabled          bool    shortcode outputs + front assets load
 *   delete_data_on_uninstall  bool    uninstall.php removes all plugin data
 */
class Settings
{
    // Where the page lives. Tools by default; 'options-general.php' for Settings,
    // 'elementor' / 'woocommerce' to sit under those. Massive plugin? Own top-level
    // menu via add_menu_page() and use its slug here.
    public const PARENT = 'tools.php';
    public const PAGE   = Plugin::SLUG;
    public const ACTION = Plugin::PREFIX . '_save_settings';
    public const NONCE  = Plugin::PREFIX . '_settings';

    public const DEFAULTS = [
        'greeting'                 => 'Hello from Clio',
        'frontend_enabled'         => true,
        'delete_data_on_uninstall' => false,
    ];

    private Plugin $plugin;

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenuPage']);
        add_action('admin_post_' . self::ACTION, [$this, 'handleSave']);
    }

    /**
     * Tabs: slug => label. Each slug renders views/settings-{slug}.php.
     * Built lazily (not in boot) so translations are loaded by the time __() runs.
     */
    public static function tabs(): array
    {
        return apply_filters(Plugin::PREFIX . '_settings_tabs', [
            'general' => __('General', 'clio-base-plugin'),
            'about'   => __('About', 'clio-base-plugin'),
        ]);
    }

    public function addMenuPage(): void
    {
        add_submenu_page(
            self::PARENT,
            __('Clio Base Plugin', 'clio-base-plugin'),
            __('Clio Base Plugin', 'clio-base-plugin'),
            Plugin::CAP,
            self::PAGE,
            [$this, 'renderPage']
        );
    }

    public function renderPage(): void
    {
        if (! current_user_can(Plugin::CAP)) {
            return;
        }

        Plugin::view('settings', [
            'plugin' => $this->plugin,
            'tabs'   => self::tabs(),
        ]);
    }

    /**
     * Handle the settings form POST (admin-post.php?action=Settings::ACTION).
     */
    public function handleSave(): void
    {
        if (! current_user_can(Plugin::CAP)) {
            wp_die(esc_html__('Unauthorized.', 'clio-base-plugin'));
        }

        check_admin_referer(self::NONCE);

        $tab     = sanitize_key($_POST['clio_tab'] ?? 'general');
        $current = get_option(Plugin::OPTION, []);

        $incoming = match ($tab) {
            'general' => $this->sanitizeGeneral($_POST),
            default   => [],
        };

        // Extra tabs added via the tabs filter sanitize their own keys here.
        $incoming = apply_filters(Plugin::PREFIX . '_save_settings_' . $tab, $incoming, $_POST);

        update_option(Plugin::OPTION, array_merge($current, $incoming));

        // Our own notice, so we don't pass ?updated=1 (core would add a duplicate).
        Notices::add(__('Settings saved.', 'clio-base-plugin'));

        wp_safe_redirect($this->plugin->settingsUrl(['tab' => $tab]));
        exit;
    }

    private function sanitizeGeneral(array $post): array
    {
        return [
            'greeting'                 => sanitize_text_field(wp_unslash($post['greeting'] ?? '')),
            'frontend_enabled'         => ! empty($post['frontend_enabled']),
            'delete_data_on_uninstall' => ! empty($post['delete_data_on_uninstall']),
        ];
    }
}
