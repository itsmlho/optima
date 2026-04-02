<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Create PMPS (Preventive Maintenance Planned Service) tables
 *
 * Creates 4 tables:
 *   pm_schedules          - PM schedule templates per unit
 *   pm_checklist_templates - Checklist items per schedule
 *   pm_jobs               - Individual PM job instances
 *   pm_job_checklists     - Checklist execution results per PM job
 *
 * PMPS integrates with Work Orders: PM jobs generate WOs with order_type='PMPS'
 */
class CreatePmTables extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS pm_schedules (
                id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                unit_id          INT UNSIGNED NOT NULL,
                schedule_name    VARCHAR(100) NOT NULL,
                trigger_type     ENUM('CALENDAR','HM','BOTH') NOT NULL DEFAULT 'CALENDAR',
                interval_days    INT NULL COMMENT 'For CALENDAR: PM every N days',
                interval_hm      INT NULL COMMENT 'For HM: PM every N hour meter',
                last_pm_date     DATE NULL,
                last_pm_hm       DECIMAL(10,1) NULL,
                next_pm_date     DATE NULL,
                next_pm_hm       DECIMAL(10,1) NULL,
                wo_category_id   INT NULL,
                wo_subcategory_id INT NULL,
                priority_id      INT NULL,
                is_active        TINYINT(1) NOT NULL DEFAULT 1,
                notes            TEXT NULL,
                created_by       INT UNSIGNED NOT NULL,
                created_at       DATETIME NULL,
                updated_at       DATETIME NULL,
                INDEX idx_pm_schedules_unit (unit_id),
                INDEX idx_pm_schedules_next_date (next_pm_date),
                INDEX idx_pm_schedules_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS pm_checklist_templates (
                id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                schedule_id      INT UNSIGNED NOT NULL,
                item_order       INT NOT NULL DEFAULT 0,
                item_name        VARCHAR(200) NOT NULL,
                item_category    VARCHAR(100) NULL,
                action_type      ENUM('CHECK','REPLACE','ADJUST','CLEAN','LUBRICATE','OTHER') NOT NULL DEFAULT 'CHECK',
                is_required      TINYINT(1) NOT NULL DEFAULT 1,
                notes            TEXT NULL,
                created_at       DATETIME NULL,
                updated_at       DATETIME NULL,
                INDEX idx_pm_checklist_schedule (schedule_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS pm_jobs (
                id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                pm_number        VARCHAR(50) NOT NULL,
                schedule_id      INT UNSIGNED NOT NULL,
                unit_id          INT UNSIGNED NOT NULL,
                due_date         DATE NOT NULL,
                due_hm           DECIMAL(10,1) NULL,
                actual_date      DATE NULL,
                actual_hm        DECIMAL(10,1) NULL,
                work_order_id    INT NULL,
                status           ENUM('SCHEDULED','IN_PROGRESS','COMPLETED','OVERDUE','SKIPPED') NOT NULL DEFAULT 'SCHEDULED',
                notes            TEXT NULL,
                created_by       INT UNSIGNED NOT NULL,
                completed_by     INT UNSIGNED NULL,
                created_at       DATETIME NULL,
                updated_at       DATETIME NULL,
                UNIQUE KEY uq_pm_number (pm_number),
                INDEX idx_pm_jobs_schedule (schedule_id),
                INDEX idx_pm_jobs_unit (unit_id),
                INDEX idx_pm_jobs_status (status),
                INDEX idx_pm_jobs_due_date (due_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS pm_job_checklists (
                id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                pm_job_id        INT UNSIGNED NOT NULL,
                template_item_id INT UNSIGNED NULL,
                item_name        VARCHAR(200) NOT NULL,
                action_type      ENUM('CHECK','REPLACE','ADJUST','CLEAN','LUBRICATE','OTHER') NOT NULL DEFAULT 'CHECK',
                result           ENUM('OK','NOT_OK','REPLACED','ADJUSTED','N/A') NOT NULL DEFAULT 'OK',
                notes            TEXT NULL,
                checked_by       INT UNSIGNED NULL,
                checked_at       DATETIME NULL,
                created_at       DATETIME NULL,
                updated_at       DATETIME NULL,
                INDEX idx_pm_job_checklists_job (pm_job_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS pm_job_checklists");
        $this->db->query("DROP TABLE IF EXISTS pm_jobs");
        $this->db->query("DROP TABLE IF EXISTS pm_checklist_templates");
        $this->db->query("DROP TABLE IF EXISTS pm_schedules");
    }
}
