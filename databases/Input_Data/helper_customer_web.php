<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helper Customer</title>
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
        }
        .customer-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .customer-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        .customer-header {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 20px;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .customer-id {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 1.2em;
            color: #667eea;
            cursor: pointer;
        }
        .customer-name {
            font-size: 1.3em;
            font-weight: 600;
            color: #333;
        }
        .contract-count {
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
        }
        .locations {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 10px;
        }
        .location-item {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            border-left: 3px solid #667eea;
        }
        .location-id {
            font-family: 'Courier New', monospace;
            color: #667eea;
            font-weight: bold;
            margin-right: 10px;
            cursor: pointer;
        }
        .location-name {
            color: #555;
        }
        .primary-badge {
            background: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.75em;
            margin-left: 5px;
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
            <h1>👥 Customer List Helper</h1>
            <a href="helper_dashboard.php" class="back-btn">← Kembali ke Dashboard</a>
        </div>
        
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="🔍 Cari berdasarkan nama customer atau kode..." autofocus>
        </div>

        <div class="info">
            <strong>💡 Tip:</strong> Klik customer_id atau location_id untuk copy ke clipboard.
        </div>
        
        <div class="results" id="results">
            <?php
            $db = new mysqli('localhost', 'root', '', 'optima_ci');
            $filter = isset($_GET['q']) ? $db->real_escape_string($_GET['q']) : '';
            
            $sql = "SELECT 
                c.id as customer_id,
                c.customer_name,
                c.customer_code,
                (SELECT COUNT(*) FROM kontrak k WHERE k.customer_id = c.id) as contract_count
            FROM customers c
            WHERE 1=1";
            
            if (!empty($filter)) {
                $sql .= " AND (customer_name LIKE '%$filter%' OR customer_code LIKE '%$filter%')";
            }
            
            $sql .= " ORDER BY customer_name";
            
            $result = $db->query($sql);
            
            if ($result->num_rows > 0) {
                while ($customer = $result->fetch_object()) {
                    echo "<div class='customer-card'>";
                    echo "<div class='customer-header'>";
                    echo "<div class='customer-id' title='Click to copy' onclick='copyText(this)'>" . $customer->customer_id . "</div>";
                    echo "<div class='customer-name'>" . htmlspecialchars($customer->customer_name) . " <small style='color:#999'>(" . htmlspecialchars($customer->customer_code ?? '-') . ")</small></div>";
                    echo "<div class='contract-count'>" . $customer->contract_count . " kontrak</div>";
                    echo "</div>";
                    
                    // Get locations
                    $loc_sql = "SELECT id as location_id, location_name, is_primary FROM customer_locations WHERE customer_id = {$customer->customer_id} ORDER BY is_primary DESC, location_name";
                    $loc_result = $db->query($loc_sql);
                    
                    if ($loc_result->num_rows > 0) {
                        echo "<div class='locations'>";
                        while ($loc = $loc_result->fetch_object()) {
                            echo "<div class='location-item'>";
                            echo "<span class='location-id' title='Click to copy' onclick='copyText(this)'>" . $loc->location_id . "</span>";
                            echo "<span class='location-name'>" . htmlspecialchars($loc->location_name) . "</span>";
                            if ($loc->is_primary) {
                                echo "<span class='primary-badge'>PRIMARY</span>";
                            }
                            echo "</div>";
                        }
                        echo "</div>";
                    } else {
                        echo "<div style='color:#999;font-style:italic;'>Tidak ada lokasi terdaftar</div>";
                    }
                    
                    echo "</div>";
                }
            } else {
                echo "<div class='no-results'>Tidak ada data customer ditemukan.</div>";
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

        // Copy text function
        function copyText(element) {
            const text = element.textContent.trim();
            navigator.clipboard.writeText(text);
            const originalText = element.textContent;
            element.textContent = '✓ Copied!';
            setTimeout(() => { element.textContent = originalText; }, 1000);
        }
    </script>
</body>
</html>
