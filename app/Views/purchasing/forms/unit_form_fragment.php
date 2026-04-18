<!-- Unit Form Fragment -->
<div class="row g-3">

    <!-- Brand (text input) -->
    <div class="col-md-6">
        <label for="unit_merk_text" class="form-label">Brand <span class="text-danger">*</span></label>
        <input type="text" id="unit_merk_text" class="form-control" placeholder="mis. TOYOTA, KOMATSU, NISSAN..." required autocomplete="off">
    </div>

    <!-- Model (text input) -->
    <div class="col-md-6">
        <label for="unit_model_text" class="form-label">Model <span class="text-danger">*</span></label>
        <input type="text" id="unit_model_text" class="form-control" placeholder="mis. 8FBN25, FD30, FG15T..." required autocomplete="off">
    </div>

    <!-- Tahun -->
    <div class="col-md-6">
        <label for="unit_tahun" class="form-label">Year</label>
        <input type="number" id="unit_tahun" class="form-control" min="1990" max="<?= date('Y') + 2 ?>" value="<?= date('Y') ?>">
    </div>

    <!-- Quantity -->
    <div class="col-md-4">
        <label for="unit_qty" class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" id="unit_qty" class="form-control" min="1" value="1" required>
    </div>

    <div class="col-12">
        <label for="unit_vendor_spec_text" class="form-label">Spesifikasi vendor — paste utuh dari baris PI</label>
        <textarea id="unit_vendor_spec_text" class="form-control font-monospace small" rows="5" placeholder="Tempel teks deskripsi baris proforma invoice tanpa dirangkum."></textarea>
        <div class="form-text">Paste teks asli dari PI. Detail seperti ukuran fork, tinggi mast, kapasitas, dsb. cukup ada di sini.</div>
    </div>
    <div class="col-12">
        <span class="form-label d-block mb-1">Isi paket</span>
        <div class="d-flex flex-wrap gap-3 align-items-start">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="pkg_fork_std" name="pkg_flags[]" value="fork_standard" checked>
                <label class="form-check-label" for="pkg_fork_std">Fork (standar pabrik)</label>
            </div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="pkg_battery" name="pkg_flags[]" value="battery"><label class="form-check-label" for="pkg_battery">Baterai</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="pkg_charger" name="pkg_flags[]" value="charger"><label class="form-check-label" for="pkg_charger">Charger</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="pkg_attachment" name="pkg_flags[]" value="attachment"><label class="form-check-label" for="pkg_attachment">Attachment</label></div>
            <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="pkg_acc" name="pkg_flags[]" value="accessories" checked><label class="form-check-label" for="pkg_acc">Aksesoris (lampu, sabuk, dll.)</label></div>
        </div>
        <div class="form-text mt-1 text-muted">Checklist lengkap per item dilengkapi saat <strong>verifikasi gudang</strong>. Centang di atas hanya menandai isi paket dari baris PI.</div>
    </div>

    <div class="col-12"><hr class="my-3"></div>
    <details class="col-12 mb-2"><summary class="fw-semibold text-muted cursor-pointer">Komponen lanjutan &amp; link master (opsional)</summary>
    <div class="row g-3 mt-1">

    <!-- Mast -->
    <div class="col-md-4">
        <label for="unit_mast" class="form-label">Mast Type</label>
        <select id="unit_mast" class="form-select select2-basic" data-master-type="mast">
            <option value="">Select Mast...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Mast</option>
            <option disabled>─────────────</option>
            <?php if (isset($masts) && is_array($masts)): ?>
                <?php foreach ($masts as $mast): ?>
                    <option value="<?= $mast['id_mast'] ?>" data-tinggi-mast="<?= esc($mast['tinggi_mast'] ?? '-') ?>">
                        <?= esc($mast['tipe_mast']) ?>
                        <?php if (!empty($mast['tinggi_mast'])): ?>
                            (<?= esc($mast['tinggi_mast']) ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <small class="text-muted" id="unit_tinggi_mast_display"></small>
    </div>

    <!-- Engine -->
    <div class="col-md-4">
        <label for="unit_mesin" class="form-label">Engine Type</label>
        <select id="unit_mesin" class="form-select select2-basic" data-master-type="engine">
            <option value="">Select Engine...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Engine</option>
            <option disabled>─────────────</option>
            <?php if (isset($mesins) && is_array($mesins)): ?>
                <?php foreach ($mesins as $mesin): ?>
                    <option value="<?= $mesin['id'] ?>">
                        <?= esc("({$mesin['merk_mesin']}) {$mesin['model_mesin']}") ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Ban -->
    <div class="col-md-4">
        <label for="unit_ban" class="form-label">Tire Type</label>
        <select id="unit_ban" class="form-select select2-basic" data-master-type="tire">
            <option value="">Select Tire...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Tire</option>
            <option disabled>─────────────</option>
            <?php if (isset($bans) && is_array($bans)): ?>
                <?php foreach ($bans as $ban): ?>
                    <option value="<?= $ban['id_ban'] ?>"><?= esc($ban['tipe_ban']) ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Roda -->
    <div class="col-md-4">
        <label for="unit_roda" class="form-label">Wheel Type</label>
        <select id="unit_roda" class="form-select select2-basic" data-master-type="wheel">
            <option value="">Select Wheel...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Wheel</option>
            <option disabled>─────────────</option>
            <?php if (isset($rodas) && is_array($rodas)): ?>
                <?php foreach ($rodas as $roda): ?>
                    <option value="<?= $roda['id_roda'] ?>"><?= esc($roda['tipe_roda']) ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <!-- Valve -->
    <div class="col-md-4">
        <label for="unit_valve" class="form-label">Valve</label>
        <select id="unit_valve" class="form-select select2-basic" data-master-type="valve">
            <option value="">Select Valve...</option>
            <option value="__ADD_NEW__" class="text-primary fw-bold" style="background-color: #f0f8ff;">➕ Add New Valve</option>
            <option disabled>─────────────</option>
            <?php if (isset($valves) && is_array($valves)): ?>
                <?php foreach ($valves as $valve): ?>
                    <option value="<?= $valve['id_valve'] ?>"><?= esc($valve['jumlah_valve']) ?> Valve</option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <?php if (!empty($baterais) && is_array($baterais)): ?>
    <div class="col-md-6">
        <label for="unit_baterai_id" class="form-label">Baterai (master)</label>
        <select id="unit_baterai_id" class="form-select select2-basic">
            <option value="">—</option>
            <?php foreach ($baterais as $b): ?>
                <option value="<?= (int)($b['id'] ?? 0) ?>"><?= esc(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? '')) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>
    <?php if (!empty($chargers) && is_array($chargers)): ?>
    <div class="col-md-6">
        <label for="unit_charger_id" class="form-label">Charger (master)</label>
        <select id="unit_charger_id" class="form-select select2-basic">
            <option value="">—</option>
            <?php foreach ($chargers as $c): ?>
                <option value="<?= (int)($c['id_charger'] ?? 0) ?>"><?= esc(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? '')) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>
    <?php if (!empty($attachments) && is_array($attachments)): ?>
    <div class="col-md-12">
        <label for="unit_attachment_id" class="form-label">Attachment (master)</label>
        <select id="unit_attachment_id" class="form-select select2-basic">
            <option value="">—</option>
            <?php foreach ($attachments as $a): ?>
                <option value="<?= (int)($a['id_attachment'] ?? 0) ?>"><?= esc(($a['tipe'] ?? '') . ' | ' . ($a['merk'] ?? '') . ' ' . ($a['model'] ?? '')) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>

    </div></details>

    <!-- Keterangan -->
    <div class="col-12">
        <label for="unit_keterangan" class="form-label">Catatan singkat (opsional)</label>
        <textarea id="unit_keterangan" class="form-control" rows="2" placeholder="Catatan operasional singkat (bukan pengganti teks PI lengkap)"></textarea>
    </div>
</div>



