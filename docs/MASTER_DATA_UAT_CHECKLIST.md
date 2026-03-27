# Master Data Center UAT Checklist

## Scope
- Entitas inti unit: `departemen`, `tipe_unit`, `jenis_unit`, `model_unit`, `kapasitas`, `tipe_mast`, `tipe_ban`, `jenis_roda`, `valve`, `status_unit`
- Komponen: `attachment`, `baterai`, `charger`, `mesin`
- Operasional tambahan: `status_attachment`, `inventory_status`, `work_order_category`, `work_order_priority`, `work_order_status`, `jenis_perintah_kerja`, `tujuan_perintah_kerja`, `status_eksekusi_workflow`

## Access & Permission
- User tanpa `view_master_data` tidak bisa mengakses `master-data/*`.
- User dengan `view_master_data` bisa membuka halaman `Master Data Center`.
- Verifikasi aksi per entitas:
  - `master_data.{entity}.view`
  - `master_data.{entity}.create`
  - `master_data.{entity}.update`
  - `master_data.{entity}.delete`

## Functional Test
- Pilih setiap entitas dari dropdown.
- Cek schema load sukses (PK/field tampil).
- Cek list data tampil.
- Cek create data baru.
- Cek update data existing.
- Cek delete data.
- Cek behavior jika tabel tidak tersedia (message clear, tidak crash).

## Compatibility Test
- Endpoint existing tetap berjalan:
  - `service/master-attachment`
  - `service/master-baterai`
  - `service/master-charger`
  - `warehouse/inventory/attachments/master/attachment`
  - `warehouse/inventory/attachments/master/baterai`
  - `warehouse/inventory/attachments/master/charger`

## Regression Test
- Marketing, Service, Warehouse flow yang memakai master komponen tidak error setelah update.
- Sidebar menampilkan link `Master Data Center` sesuai permission.

## SQL Rollout Order
1. `2026-03-27_master_data_gap_hardening.sql`
2. `2026-03-27_master_data_center_permissions.sql`

## Notes
- `model_unit` tetap dipakai sebagai pusat brand/model (tanpa split master merk).
- `customers`, `customer_locations`, `areas`, `suppliers`, `permissions` tidak diubah pada scope ini.

