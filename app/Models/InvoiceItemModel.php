<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Invoice Item Model
 * Manages invoice line items with automatic total calculation
 * Triggers handle subtotal updates to parent invoice
 */
class InvoiceItemModel extends Model
{
    protected $table = 'invoice_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'invoice_id',
        'item_type',
        'description',
        'unit_id',
        'quantity',
        'unit_price',
        'subtotal',
        'reference_contract_spec_id',
        'notes'
    ];
    
    protected $useTimestamps = false;
    
    protected $validationRules = [
        'invoice_id' => 'required|integer',
        'item_type' => 'required|in_list[UNIT_RENTAL,ATTACHMENT_RENTAL,DELIVERY_FEE,OTHER]',
        'description' => 'required|string|max_length[500]',
        'quantity' => 'required|integer',
        'unit_price' => 'required|decimal'
    ];
    
    /**
     * Add invoice items from Delivery Instruction
     * Creates line items for all delivered units and attachments
     * 
     * @param int $invoiceId Invoice ID
     * @param int $diId Delivery Instruction ID
     * @return int Number of items added
     */
    public function addItemsFromDI(int $invoiceId, int $diId): int
    {
        $diItemModel = new \App\Models\DeliveryItemModel();
        $deliveryItems = $diItemModel->select('delivery_items.*, '
                                            . 'inventory_unit.nomor_seri, inventory_unit.merk_unit, inventory_unit.model_unit, '
                                            . 'kontrak_spesifikasi.harga_per_unit_bulanan, kontrak_spesifikasi.harga_per_unit_harian')
                                     ->join('inventory_unit', 'inventory_unit.id_inventory_unit = delivery_items.unit_id', 'left')
                                     ->join('delivery_instructions di', 'di.id = delivery_items.di_id', 'left')
                                     ->join('kontrak', 'kontrak.id = di.contract_id', 'left')
                                     ->join('kontrak_spesifikasi', 'kontrak_spesifikasi.kontrak_id = kontrak.id', 'left')
                                     ->where('delivery_items.di_id', $diId)
                                     ->findAll();
        
        $itemCount = 0;
        
        foreach ($deliveryItems as $item) {
            $itemType = 'UNIT_RENTAL';
            $description = '';
            $unitPrice = 0;
            
            if ($item['item_type'] === 'UNIT' && !empty($item['nomor_seri'])) {
                $description = "Rental Unit - {$item['merk_unit']} {$item['model_unit']} ({$item['nomor_seri']})";
                $unitPrice = $item['harga_per_unit_bulanan'] ?? 0;
            } elseif ($item['item_type'] === 'ATTACHMENT') {
                $itemType = 'ATTACHMENT_RENTAL';
                $description = "Attachment - " . ($item['keterangan'] ?? 'Attachment');
                $unitPrice = 0; // Get from attachment pricing if available
            }
            
            $itemData = [
                'invoice_id' => $invoiceId,
                'item_type' => $itemType,
                'description' => $description,
                'unit_id' => $item['unit_id'],
                'quantity' => 1, // One unit per line
                'unit_price' => $unitPrice,
                'reference_contract_spec_id' => $item['reference_contract_spec_id'] ?? null,
                'notes' => $item['keterangan'] ?? null
            ];
            
            if ($this->insert($itemData)) {
                $itemCount++;
            }
        }
        
        return $itemCount;
    }
    
    /**
     * Add invoice items from Contract units (kontrak_unit → inventory_unit)
     * Digunakan untuk recurring rental invoice.
     * Harga diambil dari inventory_unit.harga_sewa_bulanan (bukan quotation_specifications).
     *
     * @param int        $contractId  Kontrak ID
     * @param float|null $amendedRate Override rate dari amendment (jika ada)
     * @return int Jumlah item yang ditambahkan
     */
    public function addItemsFromContract(int $contractId, ?float $amendedRate = null): int
    {
        // Cari invoice terakhir untuk kontrak ini
        $invoiceModel = new \App\Models\InvoiceModel();
        $invoice = $invoiceModel->where('contract_id', $contractId)
                                ->orderBy('id', 'DESC')
                                ->first();

        if (!$invoice) {
            return 0;
        }

        $invoiceId = $invoice['id'];

        // Ambil semua unit aktif dari kontrak_unit JOIN inventory_unit
        // Ini adalah sumber kebenaran: harga per unit dari inventory_unit.harga_sewa_bulanan
        $db      = \Config\Database::connect();
        $units   = $db->table('kontrak_unit ku')
                      ->select('
                          ku.unit_id,
                          iu.nomor_seri,
                          iu.merk_unit,
                          iu.model_unit,
                          iu.kapasitas,
                          iu.harga_sewa_bulanan
                      ')
                      ->join('inventory_unit iu', 'iu.id_inventory_unit = ku.unit_id')
                      ->where('ku.kontrak_id', $contractId)
                      ->where('ku.status', 'ACTIVE')
                      ->where('ku.is_temporary', 0)
                      ->get()
                      ->getResultArray();

        $itemCount = 0;

        foreach ($units as $unit) {
            // Gunakan amended rate jika ada, otherwise ambil dari unit
            $unitPrice = $amendedRate ?? (float)($unit['harga_sewa_bulanan'] ?? 0);

            $kapasitas   = $unit['kapasitas'] ? " {$unit['kapasitas']}" : '';
            $description = "Rental - {$unit['merk_unit']} {$unit['model_unit']}{$kapasitas}"
                         . " ({$unit['nomor_seri']})";

            $itemData = [
                'invoice_id'                  => $invoiceId,
                'item_type'                   => 'UNIT_RENTAL',
                'description'                 => $description,
                'unit_id'                     => $unit['unit_id'],
                'quantity'                    => 1,
                'unit_price'                  => $unitPrice,
                'subtotal'                    => $unitPrice, // qty=1 jadi sama
                'reference_contract_spec_id'  => null,
                'notes'                       => null,
            ];

            if ($this->insert($itemData)) {
                $itemCount++;
            }
        }

        // Fallback: jika tidak ada unit di kontrak_unit, gunakan quotation_specifications (legacy)
        if ($itemCount === 0) {
            $builder = $db->table('quotation_specifications');
            $specs   = $builder->where('kontrak_id', $contractId)
                               ->where('is_spare_unit !=', 1)
                               ->where('is_active', 1)
                               ->get()
                               ->getResultArray();

            foreach ($specs as $spec) {
                $unitPrice   = $amendedRate ?? $spec['monthly_price'] ?? 0;
                $description = "Rental - {$spec['specification_name']}";

                $itemData = [
                    'invoice_id'                 => $invoiceId,
                    'item_type'                  => 'UNIT_RENTAL',
                    'description'                => $description,
                    'unit_id'                    => null,
                    'quantity'                   => $spec['quantity'] ?? 1,
                    'unit_price'                 => $unitPrice,
                    'subtotal'                   => $unitPrice * ($spec['quantity'] ?? 1),
                    'reference_contract_spec_id' => $spec['id_specification'],
                    'notes'                      => null,
                ];

                if ($this->insert($itemData)) {
                    $itemCount++;
                }
            }
        }

        return $itemCount;
    }

    /**
     * Add invoice items secara manual dari daftar unit (tanpa kontrak/PO)
     * Digunakan untuk invoice MANUAL_RENTAL: admin pilih unit + harga langsung.
     *
     * @param int   $invoiceId Invoice ID
     * @param array $unitItems Array of ['unit_id' => int, 'unit_price' => float, 'notes' => string|null]
     * @return int Jumlah item yang berhasil ditambahkan
     */
    public function addItemsFromUnits(int $invoiceId, array $unitItems): int
    {
        $db        = \Config\Database::connect();
        $itemCount = 0;

        foreach ($unitItems as $item) {
            $unitId    = (int) ($item['unit_id'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);

            if ($unitId <= 0) {
                continue;
            }

            // Ambil detail unit dari inventory
            $unit = $db->table('inventory_unit')
                       ->select('nomor_seri, merk_unit, model_unit, kapasitas, harga_sewa_bulanan')
                       ->where('id_inventory_unit', $unitId)
                       ->get()
                       ->getRowArray();

            if (!$unit) {
                continue;
            }

            // Jika harga tidak diberikan, gunakan harga dari inventory_unit
            if ($unitPrice <= 0) {
                $unitPrice = (float)($unit['harga_sewa_bulanan'] ?? 0);
            }

            $kapasitas   = $unit['kapasitas'] ? " {$unit['kapasitas']}" : '';
            $description = "Rental - {$unit['merk_unit']} {$unit['model_unit']}{$kapasitas}"
                         . " ({$unit['nomor_seri']})";

            $itemData = [
                'invoice_id'                 => $invoiceId,
                'item_type'                  => 'UNIT_RENTAL',
                'description'                => $description,
                'unit_id'                    => $unitId,
                'quantity'                   => 1,
                'unit_price'                 => $unitPrice,
                'subtotal'                   => $unitPrice,
                'reference_contract_spec_id' => null,
                'notes'                      => $item['notes'] ?? null,
            ];

            if ($this->insert($itemData)) {
                $itemCount++;
            }
        }

        return $itemCount;
    }
    
    /**
     * Get items for an invoice with details
     * 
     * @param int $invoiceId Invoice ID
     * @return array List of invoice items
     */
    public function getItemsByInvoice(int $invoiceId): array
    {
        return $this->select('invoice_items.*, '
                           . 'inventory_unit.nomor_seri, inventory_unit.merk_unit')
                    ->join('inventory_unit', 'inventory_unit.id_inventory_unit = invoice_items.unit_id', 'left')
                    ->where('invoice_id', $invoiceId)
                    ->findAll();
    }
    
    /**
     * Calculate invoice total (manual - triggers should handle this automatically)
     * Use only as fallback if triggers fail
     * 
     * @param int $invoiceId Invoice ID
     * @return bool Success status
     */
    public function calculateInvoiceTotal(int $invoiceId): bool
    {
        $invoiceModel = new \App\Models\InvoiceModel();
        $invoice = $invoiceModel->find($invoiceId);
        
        if (!$invoice) {
            return false;
        }
        
        // Calculate subtotal from all items
        $items = $this->where('invoice_id', $invoiceId)->findAll();
        $subtotal = 0;
        
        foreach ($items as $item) {
            $subtotal += $item['subtotal'];
        }
        
        // Calculate tax
        $discount = $invoice['discount_amount'] ?? 0;
        $taxPercent = $invoice['tax_percent'] ?? 0;
        $taxAmount = ($subtotal - $discount) * $taxPercent / 100;
        
        // Calculate total
        $total = $subtotal - $discount + $taxAmount;
        
        // Update invoice
        return (bool) $invoiceModel->update($invoiceId, [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total
        ]);
    }
}
