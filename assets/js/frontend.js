/**
 * Clio Base Plugin — front-end
 *
 * Data from wp_localize_script: window.clioBasePlugin
 *   { i18n: { show, hide } }
 *
 * Loaded with strategy "defer", so the DOM is parsed when this runs.
 */
(function () {
    'use strict';

    var cfg = window.clioBasePlugin || { i18n: { show: 'Show details', hide: 'Hide details' } };

    document.querySelectorAll('.clio-base-plugin-box').forEach(function (box) {
        var toggle  = box.querySelector('.clio-base-plugin-toggle');
        var details = box.querySelector('.clio-base-plugin-details');

        if (! toggle || ! details) return;

        toggle.addEventListener('click', function () {
            var open = details.hidden;
            details.hidden = ! open;
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.textContent = open ? cfg.i18n.hide : cfg.i18n.show;
        });
    });
}());
