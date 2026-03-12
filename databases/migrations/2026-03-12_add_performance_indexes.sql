-- Performance Optimization Indexes
-- Added: 2026-03-12
-- Note: All indexes below already exist in the database

-- Index for work orders date filtering (created_at queries)
-- Already exists: idx_work_orders_created ON work_orders(created_at)

-- Index for delivery instructions tanggal_kirim queries
-- Already exists: idx_delivery_instructions_tanggal_kirim ON delivery_instructions(tanggal_kirim)

-- Index for quotation date range queries with stage filter
-- Already exists: idx_quotations_stage_date ON quotations(stage, quotation_date)