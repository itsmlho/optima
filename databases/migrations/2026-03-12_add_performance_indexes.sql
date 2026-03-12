-- Performance Optimization Indexes
-- Added: 2026-03-12

-- Index for invoice items date range queries (billing period filtering)
CREATE INDEX idx_invoice_items_period ON invoice_items(invoice_id, billing_period_start, billing_period_end);

-- Index for work orders date filtering (created_at queries)
CREATE INDEX idx_work_orders_created ON work_orders(created_at);

-- Index for delivery instructions tanggal_kirim queries
CREATE INDEX idx_delivery_instructions_tanggal_kirim ON delivery_instructions(tanggal_kirim);

-- Index for quotation date range queries with stage filter
CREATE INDEX idx_quotations_stage_date ON quotations(stage, quotation_date);