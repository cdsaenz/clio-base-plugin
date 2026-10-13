<?php
/**
 * View: shortcode output (Frontend::SHORTCODE)
 *
 * Rendered by Plugin::view(); variables come from its $data array.
 */

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/** @var string $greeting */
?>
<div class="clio-base-plugin-box">
    <p class="clio-base-plugin-greeting"><?php echo esc_html($greeting); ?></p>
    <button type="button" class="clio-base-plugin-toggle" aria-expanded="false"><?php esc_html_e('Show details', 'clio-base-plugin'); ?></button>
    <div class="clio-base-plugin-details" hidden>
        <p><?php esc_html_e('Rendered by Clio Base Plugin.', 'clio-base-plugin'); ?></p>
    </div>
</div>
