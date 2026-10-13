# Clio WordPress Plugin Standard

The rules for every Clio plugin. `clio-base-plugin` is the reference implementation. When the rules and the code disagree, fix one of them.

## Starting a new plugin

Copy this plugin (without `.git` or this file), rename the main file, then replace these four tokens everywhere, in this order:

`Clio Base Plugin` → Name, `clio-base-plugin` → slug, `clio_base_plugin` → snake, `BasePlugin` → ProjectId (also covers the JS globals). Then edit the header Description and README.

To bring an existing plugin up to the standard, match the structure and rules below. Don't copy files over it blindly.

## Hard rules

- **PHP 8.0+**, `Requires PHP: 8.0` in the header. Typed properties, union types, `match`, `str_starts_with` are all fine.
- **Namespaced OOP**: `Clio\<ProjectId>` (PascalCase, e.g. `Clio\FooBar`). No global functions or classes except in `uninstall.php`.
- **No autoloader, no Composer for it.** Plain `require_once` lines in `Plugin::requireIncludes()`. Only add Composer when a real third-party library needs it (PHPMailer etc.), and commit `vendor/` in that case.
- **Plain CSS/JS. No build step**: no npm, no bundlers, no Sass. Vanilla JS, and jQuery only if a WP dependency forces it.
- **No self-updater.** Deployment is handled outside the plugin. The header always has `Update URI: false`, so a wordpress.org plugin with the same slug can never overwrite ours.
- **Dependencies** go in the header as `Requires Plugins: elementor, woocommerce` (folder slugs, WP 6.5+). WordPress then blocks activation until they're active. Don't write our own "X is missing" notices.
- Every PHP file, **views included**, declares `namespace Clio\<ProjectId>;`. Views add `/** @var Type $var */` hints for the variables `Plugin::view()` passes in, which keeps Intelephense quiet.
- Every PHP file starts with `defined('ABSPATH') || exit;` (`uninstall.php` checks `WP_UNINSTALL_PLUGIN` instead).

## Naming (one ID, four forms)

| Form      | Example            | Used for                                                     |
|-----------|--------------------|--------------------------------------------------------------|
| Name      | `Clio Foo Bar`     | Header, page titles                                          |
| slug      | `clio-foo-bar`     | Folder, main file, text domain, menu slug, handles, CSS classes |
| snake     | `clio_foo_bar`     | Option keys, transients, hooks/filters, AJAX actions, shortcodes |
| Namespace | `Clio\FooBar`      | PHP                                                          |
| JS global | `clioFooBar`       | Localized script data (`clioFooBarAdmin` for admin)          |

**IDs are spelled out once**, in `Plugin` constants: `SLUG`, `PREFIX`, `JS`, plus `CAP` for the capability. Every other ID is derived from them, e.g. `Settings::NONCE = Plugin::PREFIX . '_settings'`, `Ajax::ACTION_PING = Plugin::PREFIX . '_ping'`, the hook names, transients, handles, and `Plugin::JS . 'Admin'`. Never hardcode an ID string elsewhere. Don't derive `SLUG` from `__FILE__`. The folder name isn't reliable (GitHub ZIPs extract as `slug-main/`), and the derived constants need a fixed value. Only the location values (`dir`, `url`, `basename`) come from `__FILE__`. The exceptions are the text domain inside `__()` (it has to stay a literal for the translation tools) and CSS class names.

## Layout

```
clio-foo-bar/
├── clio-foo-bar.php     header, require Plugin.php, Plugin::getInstance(), activation hooks
├── uninstall.php        data cleanup (only if user opted in)
├── includes/            one class per file, filename = class name
│   ├── Plugin.php       singleton: paths, version, requires, boot(), option(), asset(), view()
│   ├── Activator.php    activate / deactivate / idempotent install()
│   ├── Notices.php      flash notices across redirects
│   ├── Settings.php     option row, DEFAULTS, tabbed settings page, save handler
│   ├── Admin.php        admin enqueues, plugin row links
│   ├── Ajax.php         admin-ajax handlers
│   └── Frontend.php     front enqueues, shortcodes
├── views/               templates only, rendered with Plugin::view()
├── assets/css, assets/js
├── languages/
├── README.md            features, hooks, requirements, changelog
└── .gitignore
```

Features go in their own class (`includes/Duplicator.php`, `includes/Feature/Thing.php`). Each feature class has `__construct(Plugin $plugin)` and `register()`, gets a `require_once` in `Plugin::requireIncludes()`, and gets instantiated in `Plugin::boot()`.

## Wiring rules

- **Version**: comes only from the header `Version:` line, read by `Plugin`. Never keep a separate constant.
- **Activation hooks**: register them in the main file, never inside `plugins_loaded` (they won't fire there).
- **Upgrades**: `install()` must be idempotent. `Plugin::maybeUpgrade()` re-runs it when the stored version differs, because ZIP updates don't fire activation.
- **Options**: one array option, `clio_foo_bar`, with defaults in `Settings::DEFAULTS`. Read through `$plugin->option('key')`.
- **Menu**: one page, set by `Settings::PARENT`. **Tools** (`tools.php`) is the default. Use Settings (`options-general.php`) when the page is just config. Put it under the host plugin (`elementor`, `woocommerce`) when it extends that plugin. Give it its own top-level menu (`add_menu_page`) only when the plugin is massive.
- **Settings page**: saves go through `admin-post.php`, a nonce, `manage_options`, and a per-tab sanitizer, then `Notices::add('Settings saved.')` and redirect. Don't add `updated=1`, or core shows a duplicate.
- **Notices**: `Notices::add($msg, $type)` for anything shown after a redirect. Messages allow only `strong/em/code/a`.
- **Plugin row**: "Settings" goes first in the action links. Extra links (About, Support) go in the row meta.
- **Translations**: load the text domain on `init`. Don't call `__()` before `init` (WP 6.7+ warns), so build labels lazily, as `Settings::tabs()` does.

## Assets

- Version them with `filemtime` through `$plugin->asset('assets/js/x.js')`.
- Admin assets load **only on our own screens** (check `$hook`).
- Front assets: **register** them on `wp_enqueue_scripts`, and **enqueue** them only where they're used (shortcode/block render). Scripts use `['in_footer' => true, 'strategy' => 'defer']`.
- Pass PHP data with `wp_localize_script` into the `clioFooBar` global. Never inline PHP into JS files.
- Prefix CSS classes with `clio-foo-bar-` (or `clio-` inside our own wrapper).

## Security

- Escape on output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses`). Sanitize on input, with `wp_unslash` first.
- Every form or AJAX handler gets a nonce check **and** a capability check.
- AJAX: `check_ajax_referer`, then `wp_send_json_success/error`. Only add `wp_ajax_nopriv_` when it's truly needed.
- Use `$wpdb->prepare` for every query that takes a variable.
- Never `unserialize` untrusted data.

## Data lifecycle

- Deactivate: clear cron and flush rewrites. **Keep the data.**
- Uninstall: delete data **only if** `delete_data_on_uninstall` is checked.

## Style

- PSR-12-ish: 4 spaces, braces on new lines for classes and methods, `! $x` with a space.
- Comments explain *why*, not what.
- README changelog: newest first, `### x.y.z` headings.
