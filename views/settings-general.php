<?php
/**
 * View: General settings tab
 *
 * Rendered by Plugin::view(); variables come from its $data array.
 */

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/** @var Plugin $plugin */
/** @var string $activeTab */

$greeting = (string) $plugin->option('greeting');
?>
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

    <?php wp_nonce_field(Settings::NONCE); ?>
    <input type="hidden" name="action"   value="<?php echo esc_attr(Settings::ACTION); ?>">
    <input type="hidden" name="clio_tab" value="<?php echo esc_attr($activeTab); ?>">

    <table class="form-table" role="presentation">

        <tr>
            <th scope="row"><label for="clio-greeting"><?php esc_html_e('Greeting', 'clio-base-plugin'); ?></label></th>
            <td>
                <input type="text" id="clio-greeting" name="greeting" class="regular-text"
                       value="<?php echo esc_attr($greeting); ?>">
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: shortcode */
                        esc_html__('Text shown by the %s shortcode.', 'clio-base-plugin'),
                        '<code>[' . esc_html(Frontend::SHORTCODE) . ']</code>'
                    );
                    ?>
                </p>
            </td>
        </tr>

    </table>

    <?php submit_button(__('Save Changes', 'clio-base-plugin')); ?>

</form>
