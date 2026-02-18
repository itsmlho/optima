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
     * Add invoice items from Contract specifications
     * Used for recurring rental invoices
     * 
     * @param int $invoiceId Invoice ID
     * @param int $contractId Contract ID
     * @param float|null $amendedRate Override rate from amendment (if applicable)
     * @return int Number of items added
     */
    public function addItemsFromContract(int $contractId, ?float $amendedRate = null): int
    {
        // Find invoice first to get invoice_id
        $invoiceModel = new \App\Models\InvoiceModel();
        $invoice = $invoiceModel->where('contract_id', $contractId)
                                ->orderBy('id', 'DESC')
                                ->first();
        
        if (!$invoice) {
            return 0;
        }
        
        $invoiceId = $invoice['id'];
        
        // Use quotation_specifications table directly (kontrak_spesifikasi is legacy)
        $db = \Config\Database::connect();
        $builder = $db->table('quotation_specifications');
        
        // Get specs for this contract, EXCLUDING spare units (is_spare_unit = 1)
        $specs = $builder->where('kontrak_id', $contractId)
                         ->where('is_spare_unit !=', 1)  // Skip spare units
                         ->where('is_active', 1)
                         ->get()
                         ->getResultArray();
        
        $itemCount = 0;
        
        foreach ($specs as $spec) {
            // Build description from specification fields
            $description = "Rental - {$spec['specification_name']}";
            
            // Use amended rate if provided, otherwise use contract spec rate
            $unitPrice = $amendedRate ?? $spec['monthly_price'] ?? 0;
            
            $itemData = [
                'invoice_id' => $invoiceId,
                'item_type' => 'UNIT_RENTAL',
                'description' => $description,
                'unit_id' => null, // Recurring invoice not tied to specific units
                'quantity' => $spec['quantity'] ?? 1,
                'unit_price' => $unitPrice,
                'reference_contract_spec_id' => $spec['id_specification'],
                'notes' => null
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
