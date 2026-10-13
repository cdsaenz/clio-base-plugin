<?php

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

class Plugin
{
    /**
     * The only place the plugin's IDs are spelled out. Everything else
     * (options, transients, hooks, nonces, AJAX actions, handles, JS globals)
     * derives from these. Text domain strings in __() must stay literal.
     */
    public const SLUG   = 'clio-base-plugin';   // folder, text domain, menu slug, asset handles
    public const PREFIX = 'clio_base_plugin';   // options, transients, hooks, nonces, AJAX, shortcodes
    public const JS     = 'clioBasePlugin';     // localized JS globals
    public const CAP    = 'manage_options';     // capability for our pages, saves, AJAX

    public const OPTION     = self::PREFIX;
    public const DB_VERSION = self::PREFIX . '_version';

    private static ?Plugin $instance = null;

    public string $file;
    public string $dir;
    public string $url;
    public string $basename;
    public string $version;

    public static string $viewsDir;

    private function __construct(string $file)
    {
        $this->file     = $file;
        $this->dir      = plugin_dir_path($file);
        $this->url      = plugin_dir_url($file);
        $this->basename = plugin_basename($file);

        // Read from the header so it can never drift from the Version: line.
        $headerData    = get_file_data($file, ['Version' => 'Version']);
        $this->version = ! empty($headerData['Version']) ? $headerData['Version'] : '0.0.0';

        self::$viewsDir = $this->dir . 'views/';

        $this->requireIncludes();
        add_action('plugins_loaded', [$this, 'boot']);
        add_action('init', [$this, 'loadTextdomain']);
    }

    public static function getInstance(string $file = ''): self
    {
        if (self::$instance === null) {
            self::$instance = new self($file);
        }

        return self::$instance;
    }

    private function requireIncludes(): void
    {
        require_once $this->dir . 'includes/Activator.php';
        require_once $this->dir . 'includes/Notices.php';
        require_once $this->dir . 'includes/Settings.php';
        require_once $this->dir . 'includes/Admin.php';
        require_once $this->dir . 'includes/Ajax.php';
        require_once $this->dir . 'includes/Frontend.php';
    }

    public function boot(): void
    {
        $this->maybeUpgrade();

        (new Notices())->register();
        (new Settings($this))->register();
        (new Admin($this))->register();
        (new Ajax($this))->register();
        (new Frontend($this))->register();
    }

    public function loadTextdomain(): void
    {
        load_plugin_textdomain(self::SLUG, false, dirname($this->basename) . '/languages');
    }

    /**
     * Activation hooks don't run when a plugin is updated by ZIP upload or
     * file replace, so re-run the (idempotent) install routine on version change.
     */
    private function maybeUpgrade(): void
    {
        if (get_option(self::DB_VERSION) !== $this->version) {
            Activator::install();
        }
    }

    /**
     * Retrieve a single option value, falling back to Settings::DEFAULTS.
     */
    public function option(string $key, mixed $default = null): mixed
    {
        $options = wp_parse_args(get_option(self::OPTION, []), Settings::DEFAULTS);
        return $options[$key] ?? $default;
    }

    /**
     * Asset URL + filemtime version for cache-busting.
     *
     * @return array{0: string, 1: string|false}
     */
    public function asset(string $relPath): array
    {
        return [$this->url . $relPath, @filemtime($this->dir . $relPath) ?: $this->version];
    }

    public function settingsUrl(array $args = []): string
    {
        // Core parents are files (tools.php); plugin parents (elementor, woocommerce) go via admin.php.
        $base = str_ends_with(Settings::PARENT, '.php') ? Settings::PARENT : 'admin.php';

        return add_query_arg(array_merge(['page' => Settings::PAGE], $args), admin_url($base));
    }

    /**
     * Render a view from /views/, optionally returning output as a string.
     */
    public static function view(string $name, array $data = [], bool $return = false): string
    {
        $file = self::$viewsDir . ltrim($name, '/') . '.php';

        if (! file_exists($file)) {
            return '';
        }

        extract($data, EXTR_SKIP);

        if ($return) {
            ob_start();
            include $file;
            return ob_get_clean();
        }

        include $file;
        return '';
    }

    public function __clone() {}
    public function __wakeup() {}
}
