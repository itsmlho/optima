<div class="auth-network-bar">
    <button type="button" class="btn-network" id="optima-network-status-btn" aria-live="polite"
        title="<?= esc(lang('App.network_status_title')) ?>">
        <span class="optima-network-dot optima-network-dot--unknown" id="optima-network-dot" aria-hidden="true"></span>
        <span class="optima-network-label" id="optima-network-label"><?= esc(lang('App.network_unknown')) ?></span>
    </button>
</div>
<div id="optima-offline-banner" class="alert alert-warning d-none auth-banner optima-offline-banner small py-2 px-3" role="alert">
    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i><span id="optima-offline-banner-text"></span>
</div>
