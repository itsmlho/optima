-- Migration: Make audit trail FK columns nullable
-- Date: 2026-03-30
-- Reason: changed_by / verified_by reference users.id — if session user_id is
--         null or doesn't exist in users table, the INSERT fails silently and
--         rolls back the entire transaction. Making them nullable removes this
--         hard dependency while preserving audit data when available.
--
-- Run on production BEFORE deploying the matching code commit.

-- ============================================================
-- 1. work_order_status_history.changed_by
--    Drop FK fk_wosh_changed_by, allow NULL
-- ============================================================
ALTER TABLE `work_order_status_history`
  DROP FOREIGN KEY `fk_wosh_changed_by`,
  MODIFY `changed_by` INT NULL DEFAULT NULL;

-- ============================================================
-- 2. unit_verification_history.verified_by
--    Drop FK unit_verification_history_ibfk_3, allow NULL
-- ============================================================
ALTER TABLE `unit_verification_history`
  DROP FOREIGN KEY `unit_verification_history_ibfk_3`,
  MODIFY `verified_by` INT NULL DEFAULT NULL;
