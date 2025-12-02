<?php

$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "=== DATA MIGRATION: KONTRAK -> QUOTATIONS ===" . PHP_EOL;

// Step 1: Create quotations from existing kontrak
echo "Step 1: Creating quotations from kontrak..." . PHP_EOL;

$kontrak_query = "
    SELECT 
        k.id,
        k.no_kontrak,
        k.nilai_total,
        k.jenis_sewa,
        k.tanggal_mulai,
        k.tanggal_berakhir,
        k.status,
        k.dibuat_oleh,
        k.dibuat_pada,
        k.diperbarui_pada,
        cl.customer_id,
        cl.location_name,
        cl.contact_person,
        cl.phone,
        cl.email,
        cl.address,
        cl.city,
        c.customer_name
    FROM kontrak k
    LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id  
    LEFT JOIN customers c ON cl.customer_id = c.id
";

$kontrak_result = $mysqli->query($kontrak_query);
$quotation_count = 0;

if ($kontrak_result) {
    while($kontrak = $kontrak_result->fetch_assoc()) {
        // Check if quotation already exists
        $check_query = "SELECT id_quotation FROM quotations WHERE created_contract_id = " . $kontrak['id'];
        $existing = $mysqli->query($check_query);
        
        if ($existing && $existing->num_rows > 0) {
            echo "⏭️  Quotation for kontrak {$kontrak['id']} already exists" . PHP_EOL;
            continue;
        }
        
        $quotation_number = "QUO-MIG-" . str_pad($kontrak['id'], 4, '0', STR_PAD_LEFT);
        $prospect_name = $kontrak['customer_name'] ?: 'Migrated Customer';
        
        $stage = 'ACCEPTED';
        switch($kontrak['status']) {
            case 'Aktif': $stage = 'ACCEPTED'; break;
            case 'Pending': $stage = 'SENT'; break;
            case 'Dibatalkan': $stage = 'REJECTED'; break;
            default: $stage = 'ACCEPTED'; break;
        }
        
        $insert_quotation = "
            INSERT INTO quotations (
                quotation_number,
                prospect_name,
                prospect_contact_person,
                prospect_phone,
                prospect_email,
                prospect_address,
                prospect_city,
                quotation_title,
                quotation_description,
                quotation_date,
                valid_until,
                currency,
                total_amount,
                stage,
                probability_percent,
                is_deal,
                deal_date,
                created_customer_id,
                created_contract_id,
                created_by,
                created_at,
                updated_at
            ) VALUES (
                '" . $mysqli->real_escape_string($quotation_number) . "',
                '" . $mysqli->real_escape_string($prospect_name) . "',
                '" . $mysqli->real_escape_string($kontrak['contact_person'] ?: '') . "',
                '" . $mysqli->real_escape_string($kontrak['phone'] ?: '') . "',
                '" . $mysqli->real_escape_string($kontrak['email'] ?: '') . "',
                '" . $mysqli->real_escape_string($kontrak['address'] ?: '') . "',
                '" . $mysqli->real_escape_string($kontrak['city'] ?: '') . "',
                'Contract " . $mysqli->real_escape_string($kontrak['no_kontrak']) . "',
                'Migrated from contract system - Type: " . $kontrak['jenis_sewa'] . "',
                '" . $kontrak['tanggal_mulai'] . "',
                '" . $kontrak['tanggal_berakhir'] . "',
                'IDR',
                " . ($kontrak['nilai_total'] ?: 0) . ",
                '$stage',
                100,
                1,
                '" . $kontrak['tanggal_mulai'] . "',
                " . ($kontrak['customer_id'] ?: 'NULL') . ",
                " . $kontrak['id'] . ",
                " . ($kontrak['dibuat_oleh'] ?: 1) . ",
                '" . ($kontrak['dibuat_pada'] ?: date('Y-m-d H:i:s')) . "',
                '" . ($kontrak['diperbarui_pada'] ?: date('Y-m-d H:i:s')) . "'
            )
        ";
        
        if ($mysqli->query($insert_quotation)) {
            echo "✅ Created quotation: $quotation_number for kontrak {$kontrak['id']}" . PHP_EOL;
            $quotation_count++;
        } else {
            echo "❌ Failed to create quotation for kontrak {$kontrak['id']}: " . $mysqli->error . PHP_EOL;
        }
    }
}

echo "Total quotations created: $quotation_count" . PHP_EOL;

// Step 2: Migrate kontrak_spesifikasi to quotation_specifications
echo PHP_EOL . "Step 2: Migrating kontrak_spesifikasi to quotation_specifications..." . PHP_EOL;

$spec_query = "
    SELECT 
        ks.*,
        q.id_quotation,
        d.nama_departemen,
        tu.tipe,
        kap.kapasitas_unit,
        k.jenis_sewa,
        DATEDIFF(k.tanggal_berakhir, k.tanggal_mulai) as rental_days
    FROM kontrak_spesifikasi ks
    JOIN kontrak k ON ks.kontrak_id = k.id
    JOIN quotations q ON q.created_contract_id = k.id
    LEFT JOIN departemen d ON ks.departemen_id = d.id_departemen
    LEFT JOIN tipe_unit tu ON ks.tipe_unit_id = tu.id_tipe_unit  
    LEFT JOIN kapasitas kap ON ks.kapasitas_id = kap.id_kapasitas
";

$spec_result = $mysqli->query($spec_query);
$specification_count = 0;

if ($spec_result) {
    while($spec = $spec_result->fetch_assoc()) {
        // Check if specification already migrated
        $check_spec = "SELECT id_specification FROM quotation_specifications WHERE original_kontrak_spek_id = " . $spec['id'];
        $existing_spec = $mysqli->query($check_spec);
        
        if ($existing_spec && $existing_spec->num_rows > 0) {
            echo "⏭️  Specification {$spec['id']} already migrated" . PHP_EOL;
            continue;
        }
        
        // Build category name
        $category = [];
        if ($spec['nama_departemen']) $category[] = $spec['nama_departemen'];
        if ($spec['tipe']) $category[] = $spec['tipe'];
        if ($spec['kapasitas_unit']) $category[] = $spec['kapasitas_unit'];
        $category_name = !empty($category) ? implode(' - ', $category) : 'General';
        
        // Build specifications JSON
        $specifications = [];
        if ($spec['attachment_tipe']) $specifications['attachment_type'] = $spec['attachment_tipe'];
        if ($spec['attachment_merk']) $specifications['attachment_brand'] = $spec['attachment_merk'];
        if ($spec['jenis_baterai']) $specifications['battery_type'] = $spec['jenis_baterai'];
        if ($spec['aksesoris']) $specifications['accessories'] = $spec['aksesoris'];
        $spec_json = !empty($specifications) ? json_encode($specifications) : '';
        
        $rental_rate = $spec['jenis_sewa'] == 'BULANAN' ? 'MONTHLY' : 'DAILY';
        $total_price = $spec['jumlah_dibutuhkan'] * ($spec['harga_per_unit_bulanan'] ?: 0);
        
        $insert_spec = "
            INSERT INTO quotation_specifications (
                id_quotation,
                original_kontrak_id,
                original_kontrak_spek_id,
                spek_kode,
                specification_name,
                specification_description,
                category,
                quantity,
                jumlah_tersedia,
                unit,
                unit_price,
                harga_per_unit_harian,
                total_price,
                equipment_type,
                brand,
                model,
                specifications,
                rental_duration,
                rental_rate_type,
                delivery_required,
                installation_required,
                maintenance_included,
                warranty_period,
                notes,
                departemen_id,
                tipe_unit_id,
                kapasitas_id,
                charger_id,
                mast_id,
                ban_id,
                roda_id,
                valve_id,
                jenis_baterai,
                attachment_tipe,
                attachment_merk,
                sort_order,
                is_active,
                created_at,
                updated_at
            ) VALUES (
                " . $spec['id_quotation'] . ",
                " . $spec['kontrak_id'] . ",
                " . $spec['id'] . ",
                '" . $mysqli->real_escape_string($spec['spek_kode']) . "',
                '" . $mysqli->real_escape_string($spec['spek_kode'] ?: 'Migrated Specification') . "',
                '" . $mysqli->real_escape_string($spec['catatan_spek'] ?: '') . "',
                '" . $mysqli->real_escape_string($category_name) . "',
                " . $spec['jumlah_dibutuhkan'] . ",
                " . $spec['jumlah_tersedia'] . ",
                'unit',
                " . ($spec['harga_per_unit_bulanan'] ?: 0) . ",
                " . ($spec['harga_per_unit_harian'] ?: 0) . ",
                $total_price,
                '" . $mysqli->real_escape_string($spec['tipe_jenis'] ?: 'Equipment') . "',
                '" . $mysqli->real_escape_string($spec['merk_unit'] ?: '') . "',
                '" . $mysqli->real_escape_string($spec['model_unit'] ?: '') . "',
                '" . $mysqli->real_escape_string($spec_json) . "',
                " . ($spec['rental_days'] ?: 30) . ",
                '$rental_rate',
                1,
                1,
                1,
                12,
                '" . $mysqli->real_escape_string($spec['catatan_spek'] ?: '') . "',
                " . ($spec['departemen_id'] ?: 'NULL') . ",
                " . ($spec['tipe_unit_id'] ?: 'NULL') . ",
                " . ($spec['kapasitas_id'] ?: 'NULL') . ",
                " . ($spec['charger_id'] ?: 'NULL') . ",
                " . ($spec['mast_id'] ?: 'NULL') . ",
                " . ($spec['ban_id'] ?: 'NULL') . ",
                " . ($spec['roda_id'] ?: 'NULL') . ",
                " . ($spec['valve_id'] ?: 'NULL') . ",
                '" . $mysqli->real_escape_string($spec['jenis_baterai'] ?: '') . "',
                '" . $mysqli->real_escape_string($spec['attachment_tipe'] ?: '') . "',
                '" . $mysqli->real_escape_string($spec['attachment_merk'] ?: '') . "',
                " . $spec['id'] . ",
                1,
                '" . ($spec['dibuat_pada'] ?: date('Y-m-d H:i:s')) . "',
                '" . ($spec['diperbarui_pada'] ?: date('Y-m-d H:i:s')) . "'
            )
        ";
        
        if ($mysqli->query($insert_spec)) {
            echo "✅ Migrated specification: {$spec['spek_kode']} (ID: {$spec['id']})" . PHP_EOL;
            $specification_count++;
        } else {
            echo "❌ Failed to migrate specification {$spec['id']}: " . $mysqli->error . PHP_EOL;
        }
    }
}

echo "Total specifications migrated: $specification_count" . PHP_EOL;

// Step 3: Update quotation totals
echo PHP_EOL . "Step 3: Updating quotation totals..." . PHP_EOL;

$update_totals = "
    UPDATE quotations q
    JOIN (
        SELECT 
            id_quotation,
            SUM(total_price) as subtotal,
            COUNT(*) as item_count
        FROM quotation_specifications 
        WHERE is_active = 1
        GROUP BY id_quotation
    ) totals ON q.id_quotation = totals.id_quotation
    SET 
        q.subtotal = totals.subtotal,
        q.tax_amount = totals.subtotal * 0.11,
        q.total_amount = totals.subtotal + (totals.subtotal * 0.11)
    WHERE q.quotation_number LIKE 'QUO-MIG-%'
";

if ($mysqli->query($update_totals)) {
    echo "✅ Updated quotation totals" . PHP_EOL;
} else {
    echo "❌ Failed to update totals: " . $mysqli->error . PHP_EOL;
}

// Final summary
echo PHP_EOL . "=== MIGRATION COMPLETED ===" . PHP_EOL;
echo "Quotations created: $quotation_count" . PHP_EOL;
echo "Specifications migrated: $specification_count" . PHP_EOL;

$mysqli->close();

?>