<?php
/**
 * View: Advanced settings tab
 *
 * Rendered by Plugin::view(); variables come from its $data array.
 */

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/** @var Plugin $plugin */
/** @var string $activeTab */

$frontendEnabled = (bool) $plugin->option('frontend_enabled');
$deleteOnUninst  = (bool) $plugin->option('delete_data_on_uninstall');
?>
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

    <?php wp_nonce_field(Settings::NONCE); ?>
    <input type="hidden" name="action"   value="<?php echo esc_attr(Settings::ACTION); ?>">
    <input type="hidden" name="clio_tab" value="<?php echo esc_attr($activeTab); ?>">

    <table class="form-table" role="presentation">

        <tr>
            <th scope="row"><?php esc_html_e('Front-end output', 'clio-base-plugin'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="frontend_enabled" value="1" <?php checked($frontendEnabled); ?>>
                    <?php esc_html_e('Enable', 'clio-base-plugin'); ?>
                </label>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Uninstall', 'clio-base-plugin'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="delete_data_on_uninstall" value="1" <?php checked($deleteOnUninst); ?>>
                    <?php esc_html_e('Delete all plugin data when the plugin is deleted', 'clio-base-plugin'); ?>
                </label>
            </td>
        </tr>

    </table>

    <?php submit_button(__('Save Changes', 'clio-base-plugin')); ?>

</form>
