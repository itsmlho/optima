-- ============================================================================
-- OPTIMA QUOTATION SYSTEM - DATABASE MIGRATION
-- Created: December 1, 2025
-- Purpose: Complete quotation system with specifications and stages
-- ============================================================================

-- 1. Quotations Table (Main quotation data)
CREATE TABLE IF NOT EXISTS quotations (
    id_quotation INT PRIMARY KEY AUTO_INCREMENT,
    quotation_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Customer Information (before customer creation)
    prospect_name VARCHAR(255) NOT NULL,
    prospect_contact_person VARCHAR(255),
    prospect_phone VARCHAR(20),
    prospect_email VARCHAR(100),
    prospect_address TEXT,
    prospect_city VARCHAR(100),
    prospect_province VARCHAR(100),
    prospect_postal_code VARCHAR(10),
    
    -- Quotation Details
    quotation_title VARCHAR(255) NOT NULL,
    quotation_description TEXT,
    quotation_date DATE NOT NULL,
    valid_until DATE NOT NULL,
    
    -- Commercial Terms
    currency VARCHAR(3) DEFAULT 'IDR',
    subtotal DECIMAL(15,2) DEFAULT 0,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    tax_percent DECIMAL(5,2) DEFAULT 11,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) DEFAULT 0,
    
    -- Payment Terms
    payment_terms TEXT,
    delivery_terms TEXT,
    warranty_terms TEXT,
    
    -- Stage Management
    stage ENUM('DRAFT', 'SENT', 'FOLLOW_UP', 'NEGOTIATION', 'ACCEPTED', 'REJECTED', 'EXPIRED') DEFAULT 'DRAFT',
    probability_percent INT DEFAULT 50,
    expected_close_date DATE,
    
    -- Deal Conversion
    is_deal BOOLEAN DEFAULT FALSE,
    deal_date DATE NULL,
    created_customer_id INT NULL,
    created_contract_id INT UNSIGNED NULL,
    
    -- Metadata
    created_by INT NOT NULL,
    assigned_to INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_quotation_number (quotation_number),
    INDEX idx_prospect_name (prospect_name),
    INDEX idx_stage (stage),
    INDEX idx_created_by (created_by),
    INDEX idx_quotation_date (quotation_date),
    INDEX idx_valid_until (valid_until),
    
    -- Foreign Key Constraints
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (created_contract_id) REFERENCES kontrak(id) ON DELETE SET NULL
);

-- 2. Quotation Specifications Table (Moved from contracts)
CREATE TABLE IF NOT EXISTS quotation_specifications (
    id_specification INT PRIMARY KEY AUTO_INCREMENT,
    id_quotation INT NOT NULL,
    
    -- Specification Details
    specification_name VARCHAR(255) NOT NULL,
    specification_description TEXT,
    category VARCHAR(100),
    
    -- Technical Details
    quantity INT NOT NULL DEFAULT 1,
    unit VARCHAR(50) DEFAULT 'pcs',
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_price DECIMAL(15,2) NOT NULL DEFAULT 0,
    
    -- Equipment Details
    equipment_type VARCHAR(100),
    brand VARCHAR(100),
    model VARCHAR(100),
    specifications TEXT,
    
    -- Service Details
    service_duration INT, -- dalam bulan
    service_frequency VARCHAR(100),
    service_scope TEXT,
    
    -- Rental Details
    rental_duration INT, -- dalam bulan
    rental_rate_type ENUM('MONTHLY', 'YEARLY', 'DAILY', 'HOURLY') DEFAULT 'MONTHLY',
    
    -- Delivery & Installation
    delivery_required BOOLEAN DEFAULT FALSE,
    installation_required BOOLEAN DEFAULT FALSE,
    delivery_cost DECIMAL(12,2) DEFAULT 0,
    installation_cost DECIMAL(12,2) DEFAULT 0,
    
    -- Additional Options
    maintenance_included BOOLEAN DEFAULT FALSE,
    warranty_period INT DEFAULT 12, -- dalam bulan
    notes TEXT,
    
    -- Order & Status
    sort_order INT DEFAULT 0,
    is_optional BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_quotation (id_quotation),
    INDEX idx_category (category),
    INDEX idx_equipment_type (equipment_type),
    INDEX idx_sort_order (sort_order),
    
    -- Foreign Key Constraints
    FOREIGN KEY (id_quotation) REFERENCES quotations(id_quotation) ON DELETE CASCADE
);

-- 3. Quotation Stages History Table
CREATE TABLE IF NOT EXISTS quotation_stage_history (
    id_history INT PRIMARY KEY AUTO_INCREMENT,
    id_quotation INT NOT NULL,
    
    -- Stage Information
    previous_stage VARCHAR(50),
    new_stage VARCHAR(50) NOT NULL,
    stage_date DATE NOT NULL,
    
    -- Probability Tracking
    previous_probability INT,
    new_probability INT,
    
    -- Notes and Actions
    notes TEXT,
    action_taken TEXT,
    next_action TEXT,
    follow_up_date DATE,
    
    -- Contact Log
    contact_method ENUM('EMAIL', 'PHONE', 'MEETING', 'WHATSAPP', 'PRESENTATION') NULL,
    contact_person VARCHAR(255),
    contact_result TEXT,
    
    -- Metadata
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_quotation (id_quotation),
    INDEX idx_stage_date (stage_date),
    INDEX idx_new_stage (new_stage),
    INDEX idx_created_by (created_by),
    
    -- Foreign Key Constraints
    FOREIGN KEY (id_quotation) REFERENCES quotations(id_quotation) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- 4. Quotation Documents Table
CREATE TABLE IF NOT EXISTS quotation_documents (
    id_document INT PRIMARY KEY AUTO_INCREMENT,
    id_quotation INT NOT NULL,
    
    -- Document Information
    document_name VARCHAR(255) NOT NULL,
    document_type ENUM('QUOTATION_PDF', 'PROPOSAL', 'TECHNICAL_SPEC', 'PRESENTATION', 'EMAIL', 'CONTRACT_DRAFT', 'OTHER') NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,
    mime_type VARCHAR(100),
    
    -- Document Metadata
    version VARCHAR(10) DEFAULT '1.0',
    is_latest BOOLEAN DEFAULT TRUE,
    description TEXT,
    
    -- Sharing & Access
    is_shared_with_customer BOOLEAN DEFAULT FALSE,
    shared_date DATETIME NULL,
    download_count INT DEFAULT 0,
    
    -- Metadata
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_quotation (id_quotation),
    INDEX idx_document_type (document_type),
    INDEX idx_is_latest (is_latest),
    INDEX idx_uploaded_by (uploaded_by),
    
    -- Foreign Key Constraints
    FOREIGN KEY (id_quotation) REFERENCES quotations(id_quotation) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- 5. Quotation Comments/Notes Table
CREATE TABLE IF NOT EXISTS quotation_comments (
    id_comment INT PRIMARY KEY AUTO_INCREMENT,
    id_quotation INT NOT NULL,
    
    -- Comment Details
    comment_text TEXT NOT NULL,
    comment_type ENUM('INTERNAL', 'CUSTOMER_FEEDBACK', 'FOLLOW_UP', 'NEGOTIATION', 'TECHNICAL') DEFAULT 'INTERNAL',
    
    -- Visibility
    is_internal BOOLEAN DEFAULT TRUE,
    is_important BOOLEAN DEFAULT FALSE,
    
    -- Follow-up
    requires_action BOOLEAN DEFAULT FALSE,
    action_due_date DATE NULL,
    action_assigned_to INT NULL,
    action_completed BOOLEAN DEFAULT FALSE,
    
    -- Metadata
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_quotation (id_quotation),
    INDEX idx_comment_type (comment_type),
    INDEX idx_requires_action (requires_action),
    INDEX idx_created_by (created_by),
    INDEX idx_action_assigned_to (action_assigned_to),
    
    -- Foreign Key Constraints
    FOREIGN KEY (id_quotation) REFERENCES quotations(id_quotation) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (action_assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- 6. Quotation Templates Table
CREATE TABLE IF NOT EXISTS quotation_templates (
    id_template INT PRIMARY KEY AUTO_INCREMENT,
    
    -- Template Information
    template_name VARCHAR(255) NOT NULL,
    template_description TEXT,
    category VARCHAR(100),
    
    -- Template Content
    default_title VARCHAR(255),
    default_description TEXT,
    default_payment_terms TEXT,
    default_delivery_terms TEXT,
    default_warranty_terms TEXT,
    
    -- Template Settings
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,
    usage_count INT DEFAULT 0,
    
    -- Metadata
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_template_name (template_name),
    INDEX idx_category (category),
    INDEX idx_is_active (is_active),
    INDEX idx_created_by (created_by),
    
    -- Foreign Key Constraints
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- 7. Quotation Template Specifications Table
CREATE TABLE IF NOT EXISTS quotation_template_specifications (
    id_template_spec INT PRIMARY KEY AUTO_INCREMENT,
    id_template INT NOT NULL,
    
    -- Template Specification Details
    specification_name VARCHAR(255) NOT NULL,
    specification_description TEXT,
    category VARCHAR(100),
    
    -- Default Values
    default_quantity INT DEFAULT 1,
    default_unit VARCHAR(50) DEFAULT 'pcs',
    default_unit_price DECIMAL(12,2) DEFAULT 0,
    
    -- Template Settings
    is_required BOOLEAN DEFAULT TRUE,
    is_customizable BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_template (id_template),
    INDEX idx_category (category),
    INDEX idx_sort_order (sort_order),
    
    -- Foreign Key Constraints
    FOREIGN KEY (id_template) REFERENCES quotation_templates(id_template) ON DELETE CASCADE
);

-- ============================================================================
-- SAMPLE DATA INSERTION
-- ============================================================================

-- Insert Sample Quotation Templates
INSERT INTO quotation_templates (template_name, template_description, category, default_title, default_payment_terms, default_delivery_terms, default_warranty_terms, is_default, created_by) VALUES
('Standard Equipment Rental', 'Template for standard equipment rental quotations', 'RENTAL', 'Equipment Rental Quotation', '30% down payment, 70% before delivery', 'FOB Warehouse, delivery extra', '12 months warranty', TRUE, 1),
('Maintenance Service', 'Template for maintenance service quotations', 'SERVICE', 'Maintenance Service Quotation', 'Monthly payment in advance', 'Service at customer location', '30 days service guarantee', FALSE, 1),
('Equipment Purchase', 'Template for equipment sales quotations', 'SALES', 'Equipment Purchase Quotation', '50% down payment, 50% before delivery', 'Free delivery within Jakarta', '24 months manufacturer warranty', FALSE, 1);

-- Insert Sample Template Specifications
INSERT INTO quotation_template_specifications (id_template, specification_name, specification_description, category, default_quantity, default_unit, default_unit_price, sort_order) VALUES
(1, 'Excavator 20 Ton', 'Hydraulic excavator with 20 ton capacity', 'HEAVY_EQUIPMENT', 1, 'unit', 150000000, 1),
(1, 'Operator Service', 'Professional operator for excavator operation', 'SERVICE', 1, 'month', 8000000, 2),
(1, 'Maintenance Package', 'Monthly maintenance and service package', 'SERVICE', 1, 'month', 2500000, 3),
(2, 'Monthly Inspection', 'Comprehensive monthly equipment inspection', 'INSPECTION', 1, 'visit', 1500000, 1),
(2, 'Repair Service', 'Emergency repair and troubleshooting service', 'REPAIR', 1, 'package', 5000000, 2),
(3, 'Generator 100KVA', 'Diesel generator with 100KVA capacity', 'GENERATOR', 1, 'unit', 350000000, 1),
(3, 'Installation Service', 'Professional installation and commissioning', 'INSTALLATION', 1, 'package', 15000000, 2);

-- ============================================================================
-- INDEXES AND OPTIMIZATIONS
-- ============================================================================

-- Additional composite indexes for better performance
CREATE INDEX idx_quotations_stage_date ON quotations(stage, quotation_date);
CREATE INDEX idx_quotations_assigned_stage ON quotations(assigned_to, stage);
CREATE INDEX idx_quotations_prospect_search ON quotations(prospect_name, prospect_contact_person);
CREATE INDEX idx_specifications_quotation_category ON quotation_specifications(id_quotation, category);
CREATE INDEX idx_specifications_price ON quotation_specifications(unit_price, total_price);

-- ============================================================================
-- TRIGGERS FOR BUSINESS LOGIC
-- ============================================================================

-- Auto-calculate specification total price
DELIMITER //
CREATE TRIGGER tr_quotation_specifications_calculate_total 
BEFORE INSERT ON quotation_specifications
FOR EACH ROW
BEGIN
    SET NEW.total_price = NEW.quantity * NEW.unit_price;
END//

CREATE TRIGGER tr_quotation_specifications_update_total 
BEFORE UPDATE ON quotation_specifications
FOR EACH ROW
BEGIN
    SET NEW.total_price = NEW.quantity * NEW.unit_price;
END//

-- Auto-update quotation totals when specifications change
CREATE TRIGGER tr_update_quotation_total_after_spec_insert
AFTER INSERT ON quotation_specifications
FOR EACH ROW
BEGIN
    CALL sp_update_quotation_totals(NEW.id_quotation);
END//

CREATE TRIGGER tr_update_quotation_total_after_spec_update
AFTER UPDATE ON quotation_specifications
FOR EACH ROW
BEGIN
    CALL sp_update_quotation_totals(NEW.id_quotation);
END//

CREATE TRIGGER tr_update_quotation_total_after_spec_delete
AFTER DELETE ON quotation_specifications
FOR EACH ROW
BEGIN
    CALL sp_update_quotation_totals(OLD.id_quotation);
END//

DELIMITER ;

-- ============================================================================
-- STORED PROCEDURES
-- ============================================================================

-- Procedure to update quotation totals
DELIMITER //
CREATE PROCEDURE sp_update_quotation_totals(IN quotation_id INT)
BEGIN
    DECLARE v_subtotal DECIMAL(15,2) DEFAULT 0;
    DECLARE v_tax_amount DECIMAL(15,2) DEFAULT 0;
    DECLARE v_total_amount DECIMAL(15,2) DEFAULT 0;
    DECLARE v_tax_percent DECIMAL(5,2) DEFAULT 11;
    DECLARE v_discount_amount DECIMAL(15,2) DEFAULT 0;
    
    -- Calculate subtotal from specifications
    SELECT COALESCE(SUM(total_price), 0) INTO v_subtotal
    FROM quotation_specifications 
    WHERE id_quotation = quotation_id AND is_active = TRUE;
    
    -- Get current discount and tax settings
    SELECT COALESCE(discount_amount, 0), COALESCE(tax_percent, 11)
    INTO v_discount_amount, v_tax_percent
    FROM quotations 
    WHERE id_quotation = quotation_id;
    
    -- Calculate tax and total
    SET v_tax_amount = (v_subtotal - v_discount_amount) * (v_tax_percent / 100);
    SET v_total_amount = v_subtotal - v_discount_amount + v_tax_amount;
    
    -- Update quotation
    UPDATE quotations 
    SET subtotal = v_subtotal,
        tax_amount = v_tax_amount,
        total_amount = v_total_amount,
        updated_at = CURRENT_TIMESTAMP
    WHERE id_quotation = quotation_id;
END//

-- Procedure to convert quotation to customer and contract
CREATE PROCEDURE sp_convert_quotation_to_deal(IN quotation_id INT, IN user_id INT)
BEGIN
    DECLARE v_customer_id INT DEFAULT NULL;
    DECLARE v_contract_id INT DEFAULT NULL;
    DECLARE v_quotation_number VARCHAR(50);
    DECLARE v_prospect_name VARCHAR(255);
    DECLARE v_prospect_contact VARCHAR(255);
    DECLARE v_prospect_phone VARCHAR(20);
    DECLARE v_prospect_email VARCHAR(100);
    DECLARE v_prospect_address TEXT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Get quotation details
    SELECT quotation_number, prospect_name, prospect_contact_person, 
           prospect_phone, prospect_email, prospect_address
    INTO v_quotation_number, v_prospect_name, v_prospect_contact,
         v_prospect_phone, v_prospect_email, v_prospect_address
    FROM quotations 
    WHERE id_quotation = quotation_id;
    
    -- Create customer
    INSERT INTO customers (customer_name, contact_person, phone, email, address, 
                          customer_type, status, created_by, created_at)
    VALUES (v_prospect_name, v_prospect_contact, v_prospect_phone, v_prospect_email,
            v_prospect_address, 'CORPORATE', 'ACTIVE', user_id, NOW());
    
    SET v_customer_id = LAST_INSERT_ID();
    
    -- Create contract (you'll need to adjust this based on your kontrak table structure)
    INSERT INTO kontrak (no_kontrak, customer_location_id, nilai_total, 
                        tanggal_mulai, tanggal_berakhir, status, dibuat_oleh, dibuat_pada)
    SELECT CONCAT('CTR/', DATE_FORMAT(NOW(), '%Y%m%d'), '/', LPAD(id_quotation, 4, '0')),
           1, total_amount, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 12 MONTH), 'Aktif', user_id, NOW()
    FROM quotations 
    WHERE id_quotation = quotation_id;
    
    SET v_contract_id = LAST_INSERT_ID();
    
    -- Update quotation as deal
    UPDATE quotations 
    SET is_deal = TRUE,
        deal_date = CURDATE(),
        stage = 'ACCEPTED',
        created_customer_id = v_customer_id,
        created_contract_id = v_contract_id,
        updated_at = NOW()
    WHERE id_quotation = quotation_id;
    
    -- Log stage change
    INSERT INTO quotation_stage_history (id_quotation, previous_stage, new_stage, 
                                       stage_date, notes, created_by)
    VALUES (quotation_id, 'NEGOTIATION', 'ACCEPTED', CURDATE(), 
            CONCAT('Deal closed - Created Customer ID: ', v_customer_id, 
                   ', Contract ID: ', v_contract_id), user_id);
    
    COMMIT;
END//

DELIMITER ;

-- ============================================================================
-- VIEWS FOR REPORTING
-- ============================================================================

-- Quotation Summary View
CREATE VIEW v_quotation_summary AS
SELECT 
    q.id_quotation,
    q.quotation_number,
    q.prospect_name,
    q.quotation_title,
    q.quotation_date,
    q.valid_until,
    q.stage,
    q.probability_percent,
    q.total_amount,
    q.currency,
    q.is_deal,
    q.deal_date,
    CONCAT(u1.first_name, ' ', u1.last_name) AS created_by_name,
    CONCAT(u2.first_name, ' ', u2.last_name) AS assigned_to_name,
    (SELECT COUNT(*) FROM quotation_specifications WHERE id_quotation = q.id_quotation AND is_active = TRUE) AS specification_count,
    DATEDIFF(q.valid_until, CURDATE()) AS days_until_expiry,
    CASE 
        WHEN q.valid_until < CURDATE() THEN 'EXPIRED'
        WHEN DATEDIFF(q.valid_until, CURDATE()) <= 7 THEN 'EXPIRING_SOON'
        ELSE 'ACTIVE'
    END AS expiry_status
FROM quotations q
LEFT JOIN users u1 ON q.created_by = u1.id
LEFT JOIN users u2 ON q.assigned_to = u2.id;

-- ============================================================================
-- SUCCESS MESSAGE
-- ============================================================================

SELECT 'OPTIMA Quotation System Database Created Successfully!' AS message,
       'Tables: 7, Views: 1, Procedures: 2, Triggers: 5' AS components,
       'Ready for quotation management with specifications' AS status;