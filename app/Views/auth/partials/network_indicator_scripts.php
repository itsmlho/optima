<script>
window.OPTIMA_NETWORK_CONFIG = {
    pingUrl: <?= json_encode(base_url('health/ping')) ?>,
    intervalMs: 45000,
    requestTimeoutMs: 8000,
    slowLatencyMs: 400,
    labels: {
        good: <?= json_encode(lang('App.network_good')) ?>,
        slow: <?= json_encode(lang('App.network_slow')) ?>,
        offline: <?= json_encode(lang('App.network_offline')) ?>,
        server_unreachable: <?= json_encode(lang('App.network_server_unreachable')) ?>,
        checking: <?= json_encode(lang('App.network_checking')) ?>,
        unknown: <?= json_encode(lang('App.network_unknown')) ?>,
        banner_offline: <?= json_encode(lang('App.network_banner_offline')) ?>,
        banner_server: <?= json_encode(lang('App.network_banner_server')) ?>,
        status_title: <?= json_encode(lang('App.network_status_title')) ?>
    }
};
</script>
<script defer src="<?= base_url('assets/js/optima-network-status.js') ?>?v=<?= file_exists(FCPATH . 'assets/js/optima-network-status.js') ? (int) @filemtime(FCPATH . 'assets/js/optima-network-status.js') : time() ?>"></script>
