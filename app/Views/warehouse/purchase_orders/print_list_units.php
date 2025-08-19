<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css" integrity="sha512-Ez0cGzNzHR1tYAv56860NLspgUGuQw16GiOOp/I2LuTmpSK9xDXlgJz3XN4cnpXWDmkNBKXR/VDMTCnAaEooxA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @media print {
            /* Biar background Bootstrap tetap muncul di print */
            .bg-secondary {
                background-color: #6c757d !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color: white !important; /* karena text-light biasanya white */
            }

            .text-light {
                color: white !important;
            }
        }
    </style>
</head>
<body onload="window.print()" onafterprint="window.close()">
    <table class="table table-bordered table-stripped border-dark" id="table-po-unit" style="font-size:9pt;">
        <thead class="bg-secondary text-light">
            <tr>
                <th style="font-size:9pt;" class="text-center align-middle" rowspan="2">No</th>
                <th style="font-size:9pt;" class="text-center align-middle" colspan="4">Data Unit</th>
                <th style="font-size:9pt;" class="text-center align-middle" rowspan="2">Kapasitas</th>
                <th style="font-size:9pt;" class="text-center align-middle" colspan="3">Mesin</th>
                <th style="font-size:9pt;" class="text-center align-middle" colspan="3">Baterai</th>
                <th style="font-size:9pt;" class="text-center align-middle" colspan="2">Charger</th>
                <th style="font-size:9pt;" class="text-center align-middle" rowspan="2">Jenis Mast</th>
                <th style="font-size:9pt;" class="text-center align-middle" rowspan="2">Jenis Ban</th>
                <th style="font-size:9pt;" class="text-center align-middle" rowspan="2">Roda</th>
                <th style="font-size:9pt;" class="text-center align-middle" rowspan="2">Aksesoris</th>
                <th style="font-size:9pt;" class="text-center align-middle" rowspan="2">Kondisi Penjualan</th>
                <th style="font-size:9pt;" class="text-center align-middle" rowspan="2">Valve</th>
                <th style="font-size:9pt;" class="text-center align-middle" rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th style="font-size:9pt;" class="text-center align-middle">Merk</th>
                <th style="font-size:9pt;" class="text-center align-middle">Model</th>
                <th style="font-size:9pt;" class="text-center align-middle">Jenis</th>
                <th style="font-size:9pt;" class="text-center align-middle">Merk</th>
                <th style="font-size:9pt;" class="text-center align-middle">Model</th>
                <th style="font-size:9pt;" class="text-center align-middle">Bahan Bakar</th>
                <th style="font-size:9pt;" class="text-center align-middle">Merk</th> <!-- BATERAI -->
                <th style="font-size:9pt;" class="text-center align-middle">Tipe</th> <!-- BATERAI -->
                <th style="font-size:9pt;" class="text-center align-middle">Jenis</th> <!-- BATERAI -->
                <th style="font-size:9pt;" class="text-center align-middle">Merk</th> <!-- CHARGER -->
                <th style="font-size:9pt;" class="text-center align-middle">Jenis</th> <!-- CHARGER -->
                <th style="font-size:9pt;" class="text-center align-middle">Tipe</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $no = 1;
                foreach ($data as $value) {
                    echo '<tr id="row-' . $value["id_po_unit"] . '">';
                    echo '<td class="text-center align-middle">' . $no++ . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['merk_unit'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['model_unit'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['nama_tipe_unit'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['jenis_unit'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['kapasitas_unit'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['merk_mesin'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['model_mesin'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['bahan_bakar'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['merk_baterai'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['tipe_baterai'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['jenis_baterai'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['merk_charger'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['tipe_charger'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['tipe_mast'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['tipe_ban'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['tipe_roda'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['attachment'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['status_penjualan'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['jumlah_valve'] ?? "-") . '</td>';
                    echo '<td class="text-center align-middle">' . htmlspecialchars($value['keterangan'] ?? "-") . '</td>';
                    echo '</tr>';
                }
            ?>
        </tbody>
    </table>
</body>
</html>