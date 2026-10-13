<?php
/**
 * View: Settings page wrapper
 *
 * Rendered by Plugin::view(); variables come from its $data array.
 */

namespace Clio\BasePlugin;

defined('ABSPATH') || exit;

/** @var Plugin $plugin */
/** @var array<string, string> $tabs  slug => label */

$activeTab = sanitize_key($_GET['tab'] ?? array_key_first($tabs));

if (! array_key_exists($activeTab, $tabs)) {
    $activeTab = array_key_first($tabs);
}
?>
<div class="wrap clio-base-plugin-settings">

    <h1><?php esc_html_e('Clio Base Plugin', 'clio-base-plugin'); ?></h1>

    <?php if (count($tabs) > 1) : ?>
        <nav class="nav-tab-wrapper">
            <?php foreach ($tabs as $slug => $label) : ?>
                <a href="<?php echo esc_url($plugin->settingsUrl(['tab' => $slug])); ?>"
                   class="nav-tab <?php echo $activeTab === $slug ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html($label); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <div class="clio-tab-content">
        <?php
        Plugin::view('settings-' . $activeTab, [
            'plugin'    => $plugin,
            'activeTab' => $activeTab,
        ]);
        ?>
    </div>

</div>
