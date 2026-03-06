<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helper Kontrak</title>
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
        }
        .search-box input {
            width: 100%;
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
        .badge-active { background: #d4edda; color: #155724; }
        .badge-expired { background: #f8d7da; color: #721c24; }
        .badge-draft { background: #fff3cd; color: #856404; }
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Kontrak List Helper</h1>
            <a href="helper_dashboard.php" class="back-btn">← Kembali ke Dashboard</a>
        </div>
        
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="🔍 Cari berdasarkan nama customer, nomor kontrak, atau lokasi..." autofocus>
        </div>

        <div class="info">
            <strong>💡 Tip:</strong> Klik atau copy ID kontrak untuk digunakan dalam CSV input.
        </div>
        
        <div class="results" id="results">
            <?php
            $db = new mysqli('localhost', 'root', '', 'optima_ci');
            $filter = isset($_GET['q']) ? $db->real_escape_string($_GET['q']) : '';
            
            $sql = "SELECT 
                k.id,
                k.no_kontrak,
                k.rental_type,
                k.status,
                k.tanggal_mulai,
                k.tanggal_berakhir,
                k.nilai_total,
                c.customer_name,
                cl.location_name,
                (SELECT COUNT(*) FROM kontrak_unit ku WHERE ku.kontrak_id = k.id) as unit_count
            FROM kontrak k
            LEFT JOIN customers c ON k.customer_id = c.id
            LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
            WHERE 1=1";
            
            if (!empty($filter)) {
                $sql .= " AND (
                    c.customer_name LIKE '%$filter%'
                    OR k.no_kontrak LIKE '%$filter%'
                    OR cl.location_name LIKE '%$filter%'
                )";
            }
            
            $sql .= " ORDER BY k.id DESC LIMIT 100";
            
            $result = $db->query($sql);
            
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<thead><tr>
                    <th>ID</th>
                    <th>No Kontrak</th>
                    <th>Customer</th>
                    <th>Lokasi</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Berakhir</th>
                    <th>Nilai Total</th>
                    <th>Jumlah Unit</th>
                </tr></thead><tbody>";
                
                while ($row = $result->fetch_object()) {
                    $statusClass = 'badge-active';
                    if ($row->status == 'EXPIRED' || $row->status == 'INACTIVE') $statusClass = 'badge-expired';
                    if ($row->status == 'DRAFT') $statusClass = 'badge-draft';
                    
                    echo "<tr>";
                    echo "<td class='id-cell' title='Click to copy'>" . $row->id . "</td>";
                    echo "<td>" . htmlspecialchars($row->no_kontrak ?? '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row->customer_name ?? '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row->location_name ?? '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row->rental_type ?? '-') . "</td>";
                    echo "<td><span class='badge $statusClass'>" . htmlspecialchars($row->status ?? '-') . "</span></td>";
                    echo "<td>" . ($row->tanggal_mulai ? date('d/m/Y', strtotime($row->tanggal_mulai)) : '-') . "</td>";
                    echo "<td>" . ($row->tanggal_berakhir ? date('d/m/Y', strtotime($row->tanggal_berakhir)) : '-') . "</td>";
                    echo "<td>Rp " . number_format($row->nilai_total ?? 0, 0, ',', '.') . "</td>";
                    echo "<td>" . $row->unit_count . "</td>";
                    echo "</tr>";
                }
                
                echo "</tbody></table>";
            } else {
                echo "<div class='no-results'>Tidak ada data kontrak ditemukan.</div>";
            }
            ?>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        let timeout = null;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                const query = searchInput.value;
                window.location.href = '?q=' + encodeURIComponent(query);
            }, 500);
        });

        // Set initial value from URL
        const urlParams = new URLSearchParams(window.location.search);
        const q = urlParams.get('q');
        if (q) searchInput.value = q;

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
