/**
 * OPTIMA — client network indicator (navigator.onLine + latency ping to health/ping).
 * Expects window.OPTIMA_NETWORK_CONFIG from the layout (pingUrl, labels, thresholds).
 */
(function () {
    'use strict';

    var cfg = window.OPTIMA_NETWORK_CONFIG || {};
    var url = cfg.pingUrl;
    if (!url) {
        return;
    }

    var labels = cfg.labels || {};
    var slowMs = typeof cfg.slowLatencyMs === 'number' ? cfg.slowLatencyMs : 400;
    var timeoutMs = typeof cfg.requestTimeoutMs === 'number' ? cfg.requestTimeoutMs : 8000;
    var intervalMs = typeof cfg.intervalMs === 'number' ? cfg.intervalMs : 45000;

    function $(id) {
        return document.getElementById(id);
    }

    function setUI(state, extra) {
        var dot = $('optima-network-dot');
        var label = $('optima-network-label');
        var banner = $('optima-offline-banner');
        var bannerText = $('optima-offline-banner-text');
        var btn = $('optima-network-status-btn');

        var textMap = {
            good: labels.good || 'OK',
            slow: labels.slow || 'Slow',
            offline: labels.offline || 'Offline',
            server_unreachable: labels.server_unreachable || 'No server',
            checking: labels.checking || '…',
            unknown: labels.unknown || '…'
        };

        if (dot) {
            dot.className = 'optima-network-dot optima-network-dot--' + state;
        }
        if (label) {
            label.textContent = textMap[state] || state;
        }
        if (btn) {
            var tip = textMap[state] || '';
            if (extra && typeof extra.ms === 'number') {
                tip += ' (' + Math.round(extra.ms) + ' ms)';
            }
            btn.setAttribute('title', tip);
            btn.setAttribute('aria-label', (labels.status_title || 'Connection') + ': ' + tip);
        }
        if (banner && bannerText) {
            if (state === 'offline') {
                bannerText.textContent = labels.banner_offline || '';
                banner.classList.remove('d-none');
            } else if (state === 'server_unreachable') {
                bannerText.textContent = labels.banner_server || '';
                banner.classList.remove('d-none');
            } else {
                banner.classList.add('d-none');
            }
        }
    }

    function ping() {
        if (!navigator.onLine) {
            setUI('offline');
            return;
        }

        setUI('checking');

        var t0 = performance.now();
        var ac = new AbortController();
        var to = window.setTimeout(function () {
            ac.abort();
        }, timeoutMs);

        fetch(url, { method: 'GET', cache: 'no-store', signal: ac.signal, credentials: 'same-origin' })
            .then(function (r) {
                window.clearTimeout(to);
                var ms = performance.now() - t0;
                if (!r.ok) {
                    setUI('server_unreachable');
                    return;
                }
                if (ms >= slowMs) {
                    setUI('slow', { ms: ms });
                } else {
                    setUI('good', { ms: ms });
                }
            })
            .catch(function () {
                window.clearTimeout(to);
                if (!navigator.onLine) {
                    setUI('offline');
                } else {
                    setUI('server_unreachable');
                }
            });
    }

    function start() {
        setUI('unknown');
        ping();
        window.setInterval(ping, intervalMs);
    }

    window.addEventListener('online', function () {
        ping();
    });
    window.addEventListener('offline', function () {
        setUI('offline');
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
