<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrak Unit Helper Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 2em; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content { padding: 40px; }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        .card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 1.4em;
        }
        .card p { color: #666; line-height: 1.6; }
        .card .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .card .btn:hover { background: #5568d3; }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        .info-box h3 { color: #333; margin-bottom: 15px; }
        .info-box ul { margin-left: 20px; }
        .info-box li { margin: 8px 0; color: #555; }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-card .number {
            font-size: 2.5em;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-card .label {
            opacity: 0.9;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Kontrak Unit Helper Dashboard</h1>
            <p>Tools untuk membantu input data kontrak_unit secara manual</p>
        </div>
        
        <div class="content">
            <?php
            // Get database stats
            $db = new mysqli('localhost', 'root', '', 'optima_ci');
            $kontrak_count = $db->query("SELECT COUNT(*) as cnt FROM kontrak")->fetch_object()->cnt;
            $unit_count = $db->query("SELECT COUNT(*) as cnt FROM inventory_unit")->fetch_object()->cnt;
            $customer_count = $db->query("SELECT COUNT(*) as cnt FROM customers")->fetch_object()->cnt;
            $kontrak_unit_count = $db->query("SELECT COUNT(*) as cnt FROM kontrak_unit")->fetch_object()->cnt;
            ?>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="label">Total Kontrak</div>
                    <div class="number"><?= number_format($kontrak_count) ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Units</div>
                    <div class="number"><?= number_format($unit_count) ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Customers</div>
                    <div class="number"><?= number_format($customer_count) ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">Kontrak-Unit Links</div>
                    <div class="number"><?= number_format($kontrak_unit_count) ?></div>
                </div>
            </div>

            <div class="cards">
                <div class="card">
                    <h3>📋 Kontrak List</h3>
                    <p>Cari kontrak berdasarkan customer, nomor kontrak, atau lokasi. Dapatkan ID kontrak untuk input CSV.</p>
                    <a href="helper_kontrak_web.php" class="btn">Buka Helper Kontrak →</a>
                </div>
                
                <div class="card">
                    <h3>🚜 Unit List</h3>
                    <p>Cari inventory unit berdasarkan tipe, model, atau nomor unit. Cek ketersediaan dan dapatkan ID unit.</p>
                    <a href="helper_unit_web.php" class="btn">Buka Helper Unit →</a>
                </div>
                
                <div class="card">
                    <h3>👥 Customer List</h3>
                    <p>Lihat daftar customer dengan semua lokasi. Dapatkan customer_id dan location_id untuk referensi.</p>
                    <a href="helper_customer_web.php" class="btn">Buka Helper Customer →</a>
                </div>
            </div>

            <div class="info-box">
                <h3>📝 Cara Menggunakan:</h3>
                <ul>
                    <li><strong>Step 1:</strong> Buka helper untuk mencari data yang diperlukan (kontrak_id, unit_id)</li>
                    <li><strong>Step 2:</strong> Catat ID yang diperlukan dari hasil pencarian</li>
                    <li><strong>Step 3:</strong> Input data ke spreadsheet CSV dengan format: <code>kontrak_id;unit_id;harga_sewa;is_spare;tanggal_mulai;tanggal_selesai;status</code></li>
                    <li><strong>Step 4:</strong> Validate CSV dengan: <code>php validate_kontrak_unit.php namafile.csv</code></li>
                    <li><strong>Step 5:</strong> Import dengan: <code>php import_kontrak_unit_append.php namafile.csv</code></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
