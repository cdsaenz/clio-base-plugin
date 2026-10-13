# Clio Base Plugin

Short description of what this plugin does.

## Features

- **Settings** — Settings → Clio Base Plugin (tabbed: General, About).
- **Shortcode** — `[clio_base_plugin greeting="Hi"]` outputs a box; front assets load only where it's used.
- **AJAX ping** — About tab button, demonstrates nonce + capability-checked `admin-ajax`.

## Developer hooks

```php
// Add a settings tab (renders views/settings-{slug}.php).
add_filter('clio_base_plugin_settings_tabs', function (array $tabs): array {
    $tabs['mytab'] = 'My Tab';
    return $tabs;
});

// Sanitize and persist that tab's fields.
add_filter('clio_base_plugin_save_settings_mytab', function (array $incoming, array $post): array {
    return ['my_key' => sanitize_text_field(wp_unslash($post['my_key'] ?? ''))];
}, 10, 2);
```

## Requirements

- PHP 8.0+
- WordPress 6.3+

## Changelog

### 1.0.0
- Initial release.
