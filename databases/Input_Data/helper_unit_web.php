<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helper Unit</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .back-btn:hover { background: rgba(255,255,255,0.3); }
        .search-box {
            padding: 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 15px;
        }
        .search-box input {
            padding: 12px 20px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        .search-box input:focus {
            outline: none;
            border-color: #667eea;
        }
        .search-box select {
            padding: 12px 20px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            background: white;
        }
        .results {
            padding: 30px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        tr:hover { background: #f8f9fa; }
        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .badge-available { background: #d4edda; color: #155724; }
        .badge-inuse { background: #f8d7da; color: #721c24; }
        .badge-maintenance { background: #fff3cd; color: #856404; }
        .id-cell {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #667eea;
        }
        .no-results {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .info { 
            padding: 15px 30px; 
            background: #e7f3ff; 
            border-left: 4px solid #2196F3;
            margin: 20px 30px;
        }
        .stats {
            padding: 20px 30px;
            background: #f8f9fa;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        .stat-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .stat-number { font-size: 1.8em; font-weight: bold; color: #667eea; }
        .stat-label { font-size: 0.85em; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚜 Unit List Helper</h1>
            <a href="helper_dashboard.php" class="back-btn">← Kembali ke Dashboard</a>
        </div>
        
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="🔍 Cari berdasarkan nomor unit, tipe, model, atau serial number..." autofocus>
            <select id="availabilityFilter">
                <option value="">Semua Unit</option>
                <option value="available">Hanya Available</option>
                <option value="inuse">Sedang Digunakan</option>
            </select>
        </div>

        <?php
        $db = new mysqli('localhost', 'root', '', 'optima_ci');
        $filter = isset($_GET['q']) ? $db->real_escape_string($_GET['q']) : '';
        $availability = isset($_GET['avail']) ? $_GET['avail'] : '';
        
        // Get stats
        $total = $db->query("SELECT COUNT(*) as cnt FROM inventory_unit")->fetch_object()->cnt;
        $available = $db->query("SELECT COUNT(DISTINCT iu.id_inventory_unit) as cnt FROM inventory_unit iu LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE') WHERE ku.id IS NULL")->fetch_object()->cnt;
        $inuse = $total - $available;
        ?>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-number"><?= number_format($total) ?></div>
                <div class="stat-label">Total Units</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" style="color: #28a745;"><?= number_format($available) ?></div>
                <div class="stat-label">Available</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" style="color: #dc3545;"><?= number_format($inuse) ?></div>
                <div class="stat-label">In Use</div>
            </div>
        </div>

        <div class="info">
            <strong>💡 Tip:</strong> Klik ID unit untuk copy. Filter "Available" untuk melihat unit yang siap digunakan.
        </div>
        
        <div class="results" id="results">
            <?php
            $sql = "SELECT 
                iu.id_inventory_unit as id,
                iu.no_unit,
                iu.serial_number,
                tu.tipe,
                CONCAT(mu.merk_unit, ' ', mu.model_unit) as model,
                iu.fuel_type,
                su.status_unit,
                (SELECT COUNT(*) FROM kontrak_unit ku 
                 WHERE ku.unit_id = iu.id_inventory_unit 
                 AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')) as active_contracts
            FROM inventory_unit iu
            LEFT JOIN tipe_unit tu ON iu.tipe_unit_id = tu.id_tipe_unit
            LEFT JOIN model_unit mu ON iu.model_unit_id = mu.id_model_unit
            LEFT JOIN status_unit su ON iu.status_unit_id = su.id_status
            WHERE 1=1";
            
            if (!empty($filter)) {
                $sql .= " AND (
                    iu.no_unit LIKE '%$filter%'
                    OR tu.tipe LIKE '%$filter%'
                    OR mu.merk_unit LIKE '%$filter%'
                    OR mu.model_unit LIKE '%$filter%'
                    OR iu.serial_number LIKE '%$filter%'
                )";
            }
            
            $sql .= " ORDER BY iu.id_inventory_unit LIMIT 200";
            
            $result = $db->query($sql);
            
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<thead><tr>
                    <th>ID</th>
                    <th>No Unit</th>
                    <th>Tipe</th>
                    <th>Model</th>
                    <th>Serial Number</th>
                    <th>Fuel Type</th>
                    <th>Status Unit</th>
                    <th>Availability</th>
                </tr></thead><tbody>";
                
                $filtered_count = 0;
                while ($row = $result->fetch_object()) {
                    $is_available = $row->active_contracts == 0;
                    
                    // Apply availability filter
                    if ($availability == 'available' && !$is_available) continue;
                    if ($availability == 'inuse' && $is_available) continue;
                    
                    $filtered_count++;
                    
                    $availClass = $is_available ? 'badge-available' : 'badge-inuse';
                    $availText = $is_available ? 'AVAILABLE' : "IN USE ({$row->active_contracts})";
                    
                    echo "<tr>";
                    echo "<td class='id-cell' title='Click to copy'>" . $row->id . "</td>";
                    echo "<td>" . htmlspecialchars($row->no_unit ?? '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row->tipe ?? '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row->model ?? '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row->serial_number ?? '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row->fuel_type ?? '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row->status_unit ?? '-') . "</td>";
                    echo "<td><span class='badge $availClass'>$availText</span></td>";
                    echo "</tr>";
                }
                
                echo "</tbody></table>";
                
                if ($filtered_count == 0) {
                    echo "<div class='no-results'>Tidak ada unit yang sesuai filter.</div>";
                }
            } else {
                echo "<div class='no-results'>Tidak ada data unit ditemukan.</div>";
            }
            ?>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const availFilter = document.getElementById('availabilityFilter');
        let timeout = null;
        
        function updateResults() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                const query = searchInput.value;
                const avail = availFilter.value;
                window.location.href = '?q=' + encodeURIComponent(query) + '&avail=' + avail;
            }, 500);
        }
        
        searchInput.addEventListener('input', updateResults);
        availFilter.addEventListener('change', updateResults);

        // Set initial values from URL
        const urlParams = new URLSearchParams(window.location.search);
        const q = urlParams.get('q');
        const avail = urlParams.get('avail');
        if (q) searchInput.value = q;
        if (avail) availFilter.value = avail;

        // Copy ID on click
        document.querySelectorAll('.id-cell').forEach(cell => {
            cell.style.cursor = 'pointer';
            cell.addEventListener('click', function() {
                const text = this.textContent.trim();
                navigator.clipboard.writeText(text);
                const originalText = this.textContent;
                this.textContent = '✓ Copied!';
                setTimeout(() => { this.textContent = originalText; }, 1000);
            });
        });
    </script>
</body>
</html>
