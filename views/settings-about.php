<?php
/**
 * View: About tab (no form)
 *
 * Rendered by Plugin::view(); variables come from its $data array.
 */

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/** @var Plugin $plugin */
/** @var string $activeTab */
?>
<p>
    <?php
    printf(
        /* translators: %s: plugin version */
        esc_html__('Version %s', 'clio-base-plugin'),
        esc_html($plugin->version)
    );
    ?>
</p>

<p><?php esc_html_e('Check the AJAX wiring:', 'clio-base-plugin'); ?></p>

<p>
    <button type="button" class="button" id="clio-base-plugin-ping"><?php esc_html_e('Ping', 'clio-base-plugin'); ?></button>
    <span class="clio-ping-result" aria-live="polite"></span>
</p>
