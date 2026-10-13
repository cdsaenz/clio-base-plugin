/**
 * Clio Base Plugin — admin
 *
 * Data from wp_localize_script: window.clioBasePluginAdmin
 *   { ajaxUrl, action, nonce, i18n: { pinging, failed } }
 */
(function () {
    'use strict';

    var cfg = window.clioBasePluginAdmin;

    document.addEventListener('DOMContentLoaded', function () {

        var button = document.getElementById('clio-base-plugin-ping');
        if (! button || ! cfg) return;

        var result = button.parentNode.querySelector('.clio-ping-result');

        button.addEventListener('click', function () {
            var body = new FormData();
            body.append('action', cfg.action);
            body.append('nonce', cfg.nonce);

            button.disabled = true;
            result.classList.remove('is-error');
            result.textContent = cfg.i18n.pinging;

            fetch(cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
                .then(function (res) { return res.json(); })
                .then(function (json) {
                    if (! json.success) throw new Error(json.data && json.data.message);
                    result.textContent = json.data.message;
                })
                .catch(function (err) {
                    result.classList.add('is-error');
                    result.textContent = (err && err.message) || cfg.i18n.failed;
                })
                .finally(function () {
                    button.disabled = false;
                });
        });

    });
}());
