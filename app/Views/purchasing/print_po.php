<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print PO</title>
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
<body class="p-3" onload="window.print()" onafterprint="window.close()">
    <div class="row">
        <div class="col-4 mb-2">
            <div class="input-group d-flex align-items-center">
                <img src="<?= base_url("assets/images/logo-optima.ico") ?>" alt="OPTIMA" class="optima-logo" width="50px">
                <h3 class="m-0 ms-2">OPTIMA</h3>
            </div>
        </div>
        <div class="col-8 mb-2 d-flex justify-content-end">
            <table>
                <tbody>
                    <tr>
                        <td>Created at</td>
                        <td class="ps-2 pe-2">:</td>
                        <td><?= $data["po"]["created_at"] ?? "-"; ?></td>
                    </tr>
                    <tr>
                        <td>Updated at</td>
                        <td class="ps-2 pe-2">:</td>
                        <td><?= $data["po"]["updated_at"] ?? "-"; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <table>
                <tbody>
                    <tr>
                        <td style="min-width: 100px;">No PO</td>
                        <td class="ps-2 pe-2">:</td>
                        <td class="value-data"><?= $data["po"]["no_po"] ?? "-"; ?></td>
                    </tr>
                    <tr>
                        <td style="min-width: 100px;">Tanggal PO</td>
                        <td class="ps-2 pe-2">:</td>
                        <td class="value-data"><?= $data["po"]["tanggal_po"] ?? "-"; ?></td>
                    </tr>
                    <tr>
                        <td style="min-width: 100px;">Supplier</td>
                        <td class="ps-2 pe-2">:</td>
                        <td class="value-data"><?= $data["po"]["nama_supplier"] ?? "-"; ?></td>
                    </tr>
                    <tr>
                        <td style="min-width: 100px;">Tipe PO</td>
                        <td class="ps-2 pe-2">:</td>
                        <td class="value-data"><?= $data["po"]["tipe_po"] ?? "-"; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <table>
                <tbody>
                    <tr>
                        <td style="min-width: 100px;">Invoice No</td>
                        <td class="ps-2 pe-2">:</td>
                        <td class="value-data"><?= $data["po"]["invoice_no"] ?? "-"; ?></td>
                    </tr>
                    <tr>
                        <td style="min-width: 100px;">Invoice Date</td>
                        <td class="ps-2 pe-2">:</td>
                        <td class="value-data"><?= $data["po"]["invoice_date"] ?? "-"; ?></td>
                    </tr>
                    <tr>
                        <td style="min-width: 100px;">BL Date</td>
                        <td class="ps-2 pe-2">:</td>
                        <td class="value-data"><?= $data["po"]["bl_date"] ?? "-"; ?></td>
                    </tr>
                    <tr>
                        <td style="min-width: 100px;">Ketarangan</td>
                        <td class="ps-2 pe-2">:</td>
                        <td><div style="max-width:300px;overflow:auto;white-space:normal;word-wrap:break-word;" class="value-data"><?= $data["po"]["keterangan_po"] ?? "-"; ?></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-12 mt-3">
            <table class="table table-bordered table-stripped w-100 border-dark" style="font-size:9pt;">
                <thead class="bg-secondary text-light">
                    <tr>
                        <th class="text-center align-middle" rowspan="2">No</th>
                        <th class="text-center align-middle" rowspan="2">Verifikasi</th>
                        <th class="text-center align-middle" colspan="5">Data Unit</th>
                        <th class="text-center align-middle" rowspan="2">Kapasitas</th>
                        <th class="text-center align-middle" colspan="3">Mesin</th>
                        <th class="text-center align-middle" colspan="3">Baterai</th>
                        <th class="text-center align-middle" colspan="2">Charger</th>
                        <th class="text-center align-middle" rowspan="2">Jenis Mast</th>
                        <th class="text-center align-middle" rowspan="2">Jenis Ban</th>
                        <th class="text-center align-middle" rowspan="2">Roda</th>
                        <th class="text-center align-middle" rowspan="2">Aksesoris</th>
                        <th class="text-center align-middle" rowspan="2">Kondisi Penjualan</th>
                        <th class="text-center align-middle" rowspan="2">Valve</th>
                    </tr>
                    <tr>
                        <th class="text-center align-middle">Merk</th>
                        <th class="text-center align-middle">Model</th>
                        <th class="text-center align-middle">Tipe</th>
                        <th class="text-center align-middle">S/N</th>
                        <th class="text-center align-middle">Jenis</th>
                        <th class="text-center align-middle">Merk</th>
                        <th class="text-center align-middle">Model</th>
                        <th class="text-center align-middle">Bahan Bakar</th>
                        <th class="text-center align-middle">Merk</th>
                        <th class="text-center align-middle">Tipe</th>
                        <th class="text-center align-middle">Jenis</th>
                        <th class="text-center align-middle">Merk</th>
                        <th class="text-center align-middle">Tipe</th>
                    </tr>
                </thead>
                <tbody id="tbody-po-detail">
                    <?php
                    $no = 1;
                    foreach ($data["details"] as $key => $value) {
                        ?>
                        <tr>
                            <td class="align-middle text-center"><?= $no++; ?></td>
                            <td class="align-middle text-center"><?= $value["status_verifikasi"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["merk_unit"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["model_unit"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["nama_tipe_unit"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["serial_number_po"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["nama_departemen"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["kapasitas_unit"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["merk_mesin"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["model_mesin"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["bahan_bakar"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["merk_baterai"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["tipe_baterai"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["jenis_baterai"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["merk_charger"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["tipe_charger"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["tipe_mast"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["tipe_ban"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["tipe_roda"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["attachment"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["status_penjualan"] ?? "-"; ?></td>
                            <td class="align-middle text-center"><?= $value["jumlah_valve"] ?? "-"; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>