<?php
/**
 * Plugin Name: Clio Base Plugin
 * Plugin URI:  https://cliowebsites.com
 * Description: Starter skeleton for Clio plugins: settings, notices, admin/front assets, activation wiring.
 * Version:     1.1.0
 * Author:      csdev@cliowebsites
 * Author URI:  https://cliowebsites.com
 * License:     GPL2
 * Text Domain: clio-base-plugin
 * Domain Path: /languages
 * Requires at least: 6.3
 * Tested up to:      7.0
 * Requires PHP:      8.0
 * Update URI:        false
 */

// Update URI: false stops wordpress.org from "updating" us with a same-slug plugin.
// Depends on another plugin? Add a header line above, e.g.  Requires Plugins: elementor, woocommerce

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'includes/Plugin.php';

Plugin::getInstance(__FILE__);

// Activation hooks must be registered at file load, not inside plugins_loaded
// (by the time activate_plugin() includes us, plugins_loaded has already fired).
register_activation_hook(__FILE__, [Activator::class, 'activate']);
register_deactivation_hook(__FILE__, [Activator::class, 'deactivate']);
