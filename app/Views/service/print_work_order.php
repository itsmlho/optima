<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order - <?= htmlspecialchars($workOrder['work_order_number'] ?? 'N/A') ?></title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        
        /* Page break styles - FORCE BREAK */
        .page-break {
            page-break-before: always !important;
            break-before: page !important;
            display: block !important;
            clear: both;
        }
        
        .page-1 {
            page-break-after: always !important;
            break-after: page !important;
            min-height: 90vh;
        }
        
        .page-2 {
            page-break-before: always !important;
            break-before: page !important;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3; /* Mengurangi tinggi baris */
            color: #333;
        }
        
        .print-container { width: 100%; }
        
        /* --- Header --- */
        .document-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header-left { 
            display: flex; 
            align-items: center; 
            flex: 1;
        }
        
        .company-logo {
            width: 120px; /* Logo diperbesar lagi */
            height: auto;
            margin-right: 20px;
            flex-shrink: 0;
        }
        
        .company-info {
            flex: 1;
            text-align: center;
        }
        
        .company-name {
            font-size: 16pt; /* Sedikit diperbesar */
            font-weight: bold;
            color: #000;
            margin-bottom: 3px;
        }
        
        .company-tagline {
            font-size: 10pt;
            color: #666;
            font-style: italic;
        }
        
        .header-right { border: 1px solid #aaa; }
        .meta-table { border-collapse: collapse; }
        .meta-table td {
            padding: 4px 8px; /* Padding dikurangi */
            font-size: 9pt;
            border: 1px solid #aaa;
        }
        .meta-table td:first-child {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        
        .document-title {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 15px;
            color: #000;
        }
        
        /* --- Panel Konten --- */
        .content-panel {
            border: 1px solid #ccc;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .panel-title {
            font-size: 9.5pt;
            font-weight: bold;
            text-align: center;
            padding: 6px;
            border-bottom: 1px solid #ccc;
            background-color: #f5f5f5;
            color: #000;
        }
        
        .panel-body { padding: 10px; }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 15px;
        }
        .info-divider { border-left: 1px solid #eee; }
        .info-table { width: 100%; }
        .info-table td { vertical-align: top; padding: 1px 0; }
        .info-table .label { width: 85px; font-weight: bold; }
        .info-table .separator { width: 10px; }
        
        hr.section-separator {
            border: 0;
            border-top: 1px solid #eee;
            margin: 10px 0;
        }

        /* --- Tabel & Detail --- */
        .sparepart-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sparepart-table th, .sparepart-table td {
            border: 1px solid #ccc;
            padding: 5px; /* Padding dikurangi */
            text-align: left;
        }
        .sparepart-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f5f5f5;
        }

        
        /* --- Print Footer --- */
        .print-footer {
            position: fixed;
            bottom: 3mm;
            left: 8mm;
            right: 8mm;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 2mm;
            background: white;
            z-index: 1000;
            page-break-inside: avoid;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            display: none;
        }
        
        /* --- Lain-lain --- */
        @media print {
            @page {
                margin: 10mm 8mm 15mm 8mm;
                size: A4;
                @top-left { content: ""; }
                @top-center { content: ""; }
                @top-right { content: ""; }
                @bottom-left { content: ""; }
                @bottom-center { content: ""; }
                @bottom-right { content: ""; }
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .no-print { display: none !important; }
            .print-footer { display: block !important; }
            
            /* Ensure page breaks work properly */
            .page-1 {
                page-break-after: always !important;
                break-after: page !important;
            }
            
            .page-break, .page-2 {
                page-break-before: always !important;
                break-before: page !important;
                display: block !important;
                clear: both !important;
            }
            
            /* Force separation between pages */
            #verification-container {
                page-break-before: always !important;
                break-before: page !important;
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
            
            /* Hide iframe for print, let the content show */
            iframe { 
                display: block !important;
                height: auto !important;
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- PAGE 1: WORK ORDER -->
        <div class="page-1">
        
        <header class="document-header">
            <div class="header-left">
                <img src="<?= base_url('assets/images/company-logo.svg') ?>" class="company-logo" alt="Logo" onerror="this.style.display='none'"/>
                <div class="company-info">
                    <div class="company-name">PT. SARANA MITRA LUAS Tbk</div>
                    <div class="company-tagline">FORKLIFT & MATERIAL HANDLING EQUIPMENT SOLUTIONS</div>
                    <div class="company-address">Jl. Kenari Utama II Blk. C No.03 & 05A, Cibatu, Kec. Cikarang Pusat, 17550</div>
                    <div class="company-phone">Phone: (021) - 3973 9988, (021) - 8990 2188</div>
                </div>
            </div>
            <div class="header-right">
                <table class="meta-table">
                    <tr>
                        <td>No. WO</td>
                        <td><?= htmlspecialchars($workOrder['work_order_number'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td><?= date('d M Y', strtotime($workOrder['report_date_raw'] ?? $workOrder['report_date'] ?? 'now')) ?></td>
                    </tr>
                </table>
            </div>
        </header>

        <h1 class="document-title">FORMULIR WORK ORDER</h1>

        <main>
            <div class="content-panel">
                <div class="panel-title">INFORMASI & KELUHAN</div>
                <div class="panel-body">
                    <div class="info-grid">
                        <div>
                             <table class="info-table">
                                <tr>
                                    <td class="label">Perusahaan</td><td class="separator">:</td><td class="value"><?= htmlspecialchars($workOrder['unit_customer'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Alamat</td><td class="separator">:</td><td class="value"><?= htmlspecialchars($workOrder['customer_address'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Area</td><td class="separator">:</td><td class="value"><?= htmlspecialchars($workOrder['unit_area_name'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="label">PIC</td><td class="separator">:</td><td class="value"><?= htmlspecialchars($workOrder['area_pic'] ?? $workOrder['pic'] ?? '-') ?></td>
                                </tr>
                            </table>
                            <div style="border: 1px solid #ddd; padding: 10px; background-color: #f9f9f9; border-radius: 4px; margin-top: 10px;">
                                <strong style="font-size: 9.5pt; display: block; margin-bottom: 5px;">Keluhan Pelanggan:</strong>
                                <div style="font-size: 9pt; line-height: 1.4;"><?= nl2br(htmlspecialchars($workOrder['complaint_description'] ?? 'Tidak ada keluhan')) ?></div>
                            </div>
                        </div>
                        <div class="info-divider"></div>
                        <div>
                             <table class="info-table">
                                <tr>
                                    <td class="label">Unit No.</td><td class="separator">:</td><td class="value"><?= htmlspecialchars($workOrder['unit_number'] ?? '') . ', ' . htmlspecialchars($workOrder['unit_type'] ?? '') . ', ' . htmlspecialchars($workOrder['unit_capacity'] ?? '') ?></td>
                                </tr>
                                 <tr>
                                    <td class="label">Hour Meter</td><td class="separator">:</td><td class="value"><?= !empty($workOrder['hm']) ? htmlspecialchars($workOrder['hm']) : (!empty($workOrder['unit_hour_meter']) ? htmlspecialchars($workOrder['unit_hour_meter']) : '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Hari/Tanggal</td><td class="separator">:</td><td class="value"><?= date('l, d/m/Y', strtotime($workOrder['report_date_raw'] ?? $workOrder['report_date'] ?? 'now')) ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Mekanik</td><td class="separator">:</td><td class="value"><?= htmlspecialchars($workOrder['mechanic_staff_name'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Helper</td><td class="separator">:</td><td class="value"><?= htmlspecialchars($workOrder['helper_staff_name'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Kategori</td><td class="separator">:</td><td class="value"><?= htmlspecialchars($workOrder['category_name'] ?? 'Tidak dikategorikan') ?> - <?= htmlspecialchars($workOrder['subcategory_name'] ?? 'Tidak ada sub kategori') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr class="section-separator">
                </div>
            </div>

            <div class="content-panel">
                <div class="panel-title">ITEMS BROUGHT (SPAREPARTS & TOOLS)</div>
                <div class="panel-body">
                    <table class="sparepart-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 10%;">Type</th>
                                <th style="width: 30%;">Item Name</th>
                                <th style="width: 15%;">Code</th>
                                <th style="width: 10%;">QTY</th>
                                <th style="width: 30%;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $spareparts = $workOrder['spareparts'] ?? [];
                            $rowCount = 0; $minimumRows = 5;
                            if (!empty($spareparts)) {
                                foreach ($spareparts as $part) {
                                    $rowCount++;
                                    $qtyWithUnit = htmlspecialchars($part['qty']??'') . ' ' . htmlspecialchars($part['satuan']??'pcs');
                                    $itemName = htmlspecialchars($part['name']??'');
                                    
                                    // Item Type Badge (Tool or Sparepart)
                                    $itemType = $part['item_type'] ?? 'sparepart';
                                    if ($itemType === 'tool') {
                                        $typeBadge = '<span style="background-color: #6c757d; color: #fff; padding: 2px 6px; font-size: 7pt; border-radius: 3px; font-weight: bold;">🔧 TOOL</span>';
                                    } else {
                                        $typeBadge = '<span style="background-color: #28a745; color: #fff; padding: 2px 6px; font-size: 7pt; border-radius: 3px; font-weight: bold;">⚙ PART</span>';
                                    }
                                    
                                    // Source Badge (Warehouse or Non-Warehouse)
                                    $isFromWarehouse = isset($part['is_from_warehouse']) ? (int)$part['is_from_warehouse'] : 1;
                                    if ($isFromWarehouse == 0) {
                                        $itemName .= ' <span style="background-color: #ffc107; color: #000; padding: 2px 6px; font-size: 7pt; border-radius: 3px; font-weight: bold; margin-left: 5px;">♻ NON-WH</span>';
                                    }
                                    
                                    $itemNotes = htmlspecialchars($part['notes'] ?? '-');
                                    
                                    echo '<tr>
                                            <td style="text-align: center;">'.$rowCount.'</td>
                                            <td style="text-align: center;">'.$typeBadge.'</td>
                                            <td>'.$itemName.'</td>
                                            <td>'.htmlspecialchars($part['code']??'-').'</td>
                                            <td style="text-align: center;">'.$qtyWithUnit.'</td>
                                            <td><small>'.$itemNotes.'</small></td>
                                          </tr>';
                                }
                            }
                            for ($i = $rowCount + 1; $i <= $minimumRows; $i++) {
                                echo '<tr><td style="text-align: center;">'.$i.'</td><td></td><td>&nbsp;</td><td></td><td></td><td></td></tr>';
                            } ?>
                        </tbody>
                    </table>
                    <div style="margin-top: 10px;">
                        <strong style="font-size: 9pt;">Detail Perbaikan:</strong>
                        <div style="border: 1px solid #ccc; padding: 15px; min-height: 60px; line-height: 1.8;">
                            <?= nl2br(htmlspecialchars($workOrder['repair_description'] ?? '')) ?>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="section-separator">
            </br>
            <!-- Professional Signature Layout: Tanggal → Jabatan → TTD → Garis → Nama -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-top: 15px; font-size: 9pt;">
                <div style="text-align: center;">
                    <div style="font-size: 8pt; margin-bottom: 5px;">Tgl: <?= isset($workOrder['report_date_raw']) ? date('d/m/Y', strtotime($workOrder['report_date_raw'])) : (isset($workOrder['report_date']) ? date('d/m/Y', strtotime($workOrder['report_date'])) : '___/___/_____') ?></div>
                    <div style="font-weight: bold; margin-bottom: 8px;">Admin</div>
                    <div style="border-bottom: 1px solid #000; width: 140px; margin: 0 auto 5px; height: 50px;"></div>
                    <div style="font-size: 8pt; margin-top: 3px;">Nama: <?= htmlspecialchars($workOrder['admin_staff_name'] ?? '________________') ?></div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 8pt; margin-bottom: 5px;">Tgl: ___/___/_____</div>
                    <div style="font-weight: bold; margin-bottom: 8px;">Gudang</div>
                    <div style="border-bottom: 1px solid #000; width: 140px; margin: 0 auto 5px; height: 50px;"></div>
                    <div style="font-size: 8pt; margin-top: 3px;">Nama: ________________</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 8pt; margin-bottom: 5px;">Tgl: <?= isset($workOrder['report_date_raw']) ? date('d/m/Y', strtotime($workOrder['report_date_raw'])) : (isset($workOrder['report_date']) ? date('d/m/Y', strtotime($workOrder['report_date'])) : '___/___/_____') ?></div>
                    <div style="font-weight: bold; margin-bottom: 8px;">Mekanik</div>
                    <div style="border-bottom: 1px solid #000; width: 140px; margin: 0 auto 5px; height: 50px;"></div>
                    <div style="font-size: 8pt; margin-top: 3px;">Nama: <?= htmlspecialchars($workOrder['mechanic_staff_name'] ?? '________________') ?></div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 8pt; margin-bottom: 5px;">Tgl: ___/___/_____</div>
                    <div style="font-weight: bold; margin-bottom: 8px;">Customer</div>
                    <div style="border-bottom: 1px solid #000; width: 140px; margin: 0 auto 5px; height: 50px;"></div>
                    <div style="font-size: 8pt; margin-top: 3px;">Nama: ________________</div>
                </div>
            </div>
                </div>
        </main>
        </div>
        <!-- END PAGE 1 -->
    
    <!-- PAGE 2: VERIFICATION (content will be loaded here) -->
    <div class="page-break page-2" id="verification-container" style="page-break-before: always !important; break-before: page !important; margin-top: 0; padding-top: 20px;">
        <div id="verification-content">
            <!-- Content will be loaded here via fetch -->
            <div style="text-align: center; padding: 50px; color: #666;">
                Loading verification data...
            </div>
        </div>
    </div>
        
        <!-- Print Footer -->
        <div class="print-footer" id="printFooter">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="text-align: left; font-size: 8px;">
                    <strong>PT SARANA MITRA LUAS Tbk</strong><br>
                    <span style="color: #888;">Sistem OPTIMA - Work Order Management</span>
                </div>
                <div style="text-align: center; font-size: 8px;">
                    <span id="printDate">Tanggal Cetak: <?= date('d/m/Y H:i') ?></span><br>
                    <span style="color: #888;">Dokumen ini dibuat secara otomatis oleh sistem OPTIMA</span>
                </div>
                <div style="text-align: right; font-size: 8px;">
                    <span id="pageInfo">Halaman <span id="currentPage">1</span></span><br>
                    <span style="color: #888;">WO No: <?= htmlspecialchars($workOrder['work_order_number'] ?? 'Unknown') ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let verificationLoaded = false;
        
        // Load verification content via fetch
        function loadVerificationContent(workOrderId) {
            console.log('Loading verification content for WO:', workOrderId);
            
            // First, load the verification data directly
            console.log('Step 1: Loading verification data...');
            fetch('<?= base_url("service/work-orders/get-unit-verification-data") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'work_order_id=' + workOrderId
            })
            .then(response => {
                console.log('Data fetch response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('📊 Verification data received:', data);
                if (data.success && data.data) {
                    // Store the data for later use
                    window.verificationData = data.data;
                    console.log('✅ Data stored for later use');
                    
                    // Now load the HTML content
                    return fetch('<?= base_url("service/work-orders/print-verification") ?>?wo_id=' + workOrderId + '&embedded=1');
                } else {
                    throw new Error('Failed to load verification data: ' + (data.message || 'Unknown error'));
                }
            })
            .then(response => {
                console.log('HTML fetch response status:', response.status);
                if (!response.ok) {
                    throw new Error('HTML response not ok: ' + response.status);
                }
                return response.text();
            })
            .then(html => {
                console.log('HTML received, length:', html.length);
                // Extract body content from verification page
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Get all content from verification page
                const bodyContent = doc.body.innerHTML;
                console.log('Body content extracted, length:', bodyContent.length);
                
                // Insert into verification container
                const container = document.getElementById('verification-content');
                if (container) {
                    container.innerHTML = bodyContent;
                    console.log('Content inserted into container');
                } else {
                    console.error('verification-content container not found');
                }
                
                // Copy ALL styles from verification page
                const verificationStyles = doc.head.querySelectorAll('style');
                verificationStyles.forEach(style => {
                    document.head.appendChild(style.cloneNode(true));
                    console.log('Style copied');
                });
                
                // Now populate the data immediately after content is inserted
                setTimeout(function() {
                    console.log('Step 2: Populating verification data...');
                    if (window.verificationData) {
                        populateVerificationData(window.verificationData);
                        console.log('✅ Data populated successfully');
                    } else {
                        console.error('❌ No verification data available');
                    }
                    
                    verificationLoaded = true;
                    console.log('Verification content loaded successfully');
                    
                    // Trigger print after content is loaded
                    setTimeout(function() {
                        initiatePrint();
                    }, 1000);
                }, 500);
            })
            .catch(error => {
                console.error('Error loading verification content:', error);
                document.getElementById('verification-content').innerHTML = 
                    '<div style="text-align: center; padding: 50px; color: red;">Error loading verification data: ' + error.message + '</div>';
                
                // Still allow print even if verification fails
                verificationLoaded = true;
                setTimeout(function() {
                    initiatePrint();
                }, 1000);
            });
        }

        // Function to populate verification data manually
        function populateVerificationData(data) {
            console.log('Populating verification data manually:', data);
            
            let unitData = data.unit || {};
            let workOrderData = data.work_order || {};
            let attachmentData = data.attachment || {};
            let accessories = data.accessories || [];
            
            console.log('Unit data:', unitData);
            console.log('Work order data:', workOrderData);
            console.log('Attachment data:', attachmentData);
            console.log('Accessories data:', accessories);
            
            // Populate header values
            const woNumber = workOrderData.work_order_number || workOrderData.wo_number || '-';
            console.log('WO Number:', woNumber);
            
            // Wait a bit for DOM to be ready
            setTimeout(function() {
                const elements = [
                    { id: 'print-wo-number', value: woNumber },
                    { id: 'print-unit-number', value: unitData.no_unit },
                    { id: 'footer-wo-number', value: woNumber },
                    
                    // Unit data
                    { id: 'db-no-unit', value: unitData.no_unit },
                    { id: 'db-pelanggan', value: unitData.pelanggan },
                    { id: 'db-lokasi', value: unitData.lokasi },
                    { id: 'db-serial-number', value: unitData.serial_number },
                    { id: 'db-tahun-unit', value: unitData.tahun_unit },
                    { id: 'db-departemen', value: unitData.departemen_name },
                    { id: 'db-tipe-unit', value: unitData.tipe_unit_name },
                    { id: 'db-model-unit', value: unitData.model_unit_name },
                    { id: 'db-kapasitas-unit', value: unitData.kapasitas_name },
                    { id: 'db-keterangan', value: unitData.keterangan },
                    { id: 'db-hour-meter', value: unitData.hour_meter },
                    
                    // Machine data
                    { id: 'db-model-mesin', value: unitData.model_mesin_name },
                    { id: 'db-sn-mesin', value: unitData.sn_mesin },
                    { id: 'db-model-mast', value: unitData.model_mast_name },
                    { id: 'db-sn-mast', value: unitData.sn_mast },
                    { id: 'db-tinggi-mast', value: unitData.tinggi_mast },
                    
                    // Attachment data
                    { id: 'db-attachment', value: attachmentData.attachment_name },
                    { id: 'db-sn-attachment', value: attachmentData.sn_attachment },
                    { id: 'db-baterai', value: attachmentData.baterai_name },
                    { id: 'db-sn-baterai', value: attachmentData.sn_baterai },
                    { id: 'db-charger', value: attachmentData.charger_name },
                    { id: 'db-sn-charger', value: attachmentData.sn_charger }
                ];
                
                let updatedCount = 0;
                let notFoundCount = 0;
                
                elements.forEach(item => {
                    const element = document.getElementById(item.id);
                    if (element) {
                        element.textContent = item.value || '-';
                        console.log(`✅ Updated ${item.id} to: ${item.value || '-'}`);
                        updatedCount++;
                    } else {
                        console.log(`❌ Element ${item.id} not found`);
                        notFoundCount++;
                    }
                });
                
                // Populate Accessories - call the function from verification page
                console.log('🔧 Populating accessories from combined print:', accessories);
                if (typeof populateAccessories === 'function') {
                    populateAccessories(accessories);
                    console.log('✅ Called populateAccessories() function');
                } else {
                    // If function not available, manually populate
                    console.log('⚠️ populateAccessories() not available, populating manually');
                    let checkedCount = 0;
                    
                    // Clear all checkboxes first
                    document.querySelectorAll('.accessory-checkbox').forEach(checkbox => {
                        checkbox.textContent = '☐';
                        checkbox.classList.remove('checked');
                    });
                    
                    // Check accessories that exist in database
                    if (accessories && accessories.length > 0) {
                        accessories.forEach(function(accessory) {
                            let accessoryValue = accessory.name || accessory.accessory_name || accessory;
                            console.log('🔍 Looking for accessory:', accessoryValue);
                            
                            // Find checkbox with matching data-accessory value
                            let checkbox = document.querySelector(`[data-accessory="${accessoryValue}"]`);
                            if (checkbox) {
                                checkbox.textContent = '✓';
                                checkbox.classList.add('checked');
                                checkedCount++;
                                console.log('✅ Found match for:', accessoryValue);
                            } else {
                                console.log('❌ No match found for:', accessoryValue);
                            }
                        });
                    }
                    
                    // Update summary
                    const summaryElement = document.getElementById('accessories-summary');
                    if (summaryElement) {
                        summaryElement.textContent = `${checkedCount} aksesoris`;
                        console.log(`📊 Updated summary: ${checkedCount} aksesoris`);
                    }
                    
                    console.log(`📊 Auto-checked ${checkedCount} accessories out of ${accessories.length}`);
                }
                
                console.log(`Verification data population completed: ${updatedCount} updated, ${notFoundCount} not found`);
                
                // If many elements not found, try again after a short delay
                if (notFoundCount > 5) {
                    console.log('Many elements not found, retrying in 1 second...');
                    setTimeout(function() {
                        populateVerificationData(data);
                    }, 1000);
                }
            }, 100);
        }

        // Auto print functionality
        function initiatePrint() {
            if (window.matchMedia && window.matchMedia('print').matches) {
                return;
            }
            
            // Only print if verification is loaded
            if (!verificationLoaded) {
                console.log('Waiting for verification to load...');
                setTimeout(initiatePrint, 500);
                return;
            }
            
            setTimeout(function() {
                try {
                    window.print();
                } catch (e) {
                    console.log('Print failed:', e);
                }
            }, 500);
        }
        
        // Initialize on page load
        const workOrderId = '<?= $workOrder['id'] ?? '' ?>';
        
        // Load verification data immediately using PHP data if available
        <?php if (isset($workOrder['id'])): ?>
        // Try to load verification data directly from PHP
        fetch('<?= base_url("service/work-orders/get-unit-verification-data") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'work_order_id=<?= $workOrder['id'] ?>'
        })
        .then(response => response.json())
        .then(data => {
            console.log('📊 Direct verification data received:', data);
            if (data.success && data.data) {
                window.verificationData = data.data;
                console.log('✅ Verification data stored for later use');
            }
        })
        .catch(error => {
            console.error('❌ Error loading verification data directly:', error);
        });
        <?php endif; ?>
        
        if (document.readyState === 'complete') {
            if (workOrderId) {
                loadVerificationContent(workOrderId);
            }
        } else {
            window.addEventListener('load', function() {
                if (workOrderId) {
                    loadVerificationContent(workOrderId);
                }
            });
            
            document.addEventListener('DOMContentLoaded', function() {
                if (workOrderId) {
                    loadVerificationContent(workOrderId);
                }
            });
        }
        
        // Show footer when printing
        window.addEventListener('beforeprint', () => {
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'block';
        });
        
        window.addEventListener('afterprint', () => {
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'none';
            
            // Auto close after print
            setTimeout(function() {
                window.close();
            }, 100);
        });
        
        // Auto print on load
        window.addEventListener('load', () => {
            const woNumber = '<?= str_replace('/', '-', htmlspecialchars($workOrder['work_order_number'] ?? 'Unknown')) ?>';
            document.title = 'WO-' + woNumber + ' - Complaint Form & Verification';
            const footer = document.getElementById('printFooter');
            if (footer) footer.style.display = 'block';
        });
    </script>
</body>
</html>