-- ================================================================
-- KPI ĐÁNH GIÁ NHÂN SỰ - SQL Migration
-- Chạy file này trong phpMyAdmin hoặc MySQL CLI
-- ================================================================

-- 1. Kỳ đánh giá
CREATE TABLE IF NOT EXISTS tbl_kpi_periods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    period_type ENUM('week','month','quarter','six_month','nine_month','year','probation','promotion','level_up') NOT NULL,
    date_start DATE NOT NULL,
    date_end DATE NOT NULL,
    status ENUM('draft','active','closed') DEFAULT 'draft',
    created_by INT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Phiếu đánh giá KPI
CREATE TABLE IF NOT EXISTS tbl_kpi_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    period_id INT NOT NULL,
    staff_id INT NOT NULL,
    department_id INT NULL,
    evaluation_type ENUM('weekly','monthly','3_month','6_month','9_month','12_month','probation','level_up','promotion') NOT NULL,
    current_level VARCHAR(50) NULL,
    target_level VARCHAR(50) NULL,

    gate1_result ENUM('pass','fail') DEFAULT 'fail',
    gate1_note TEXT NULL,

    total_tasks INT DEFAULT 0,
    completed_tasks INT DEFAULT 0,
    ontime_tasks INT DEFAULT 0,
    overdue_tasks INT DEFAULT 0,
    completion_rate DECIMAL(10,2) DEFAULT 0,
    ontime_rate DECIMAL(10,2) DEFAULT 0,
    process_compliance_rate DECIMAL(10,2) DEFAULT 0,

    p2_performance DECIMAL(10,2) DEFAULT 0,
    gate3_compliance DECIMAL(10,2) DEFAULT 0,
    gate4_capability DECIMAL(10,2) DEFAULT 0,
    contribution_bonus DECIMAL(10,2) DEFAULT 0,
    p3_final DECIMAL(10,2) DEFAULT 0,
    total_score DECIMAL(10,2) DEFAULT 0,

    hard_fail TINYINT(1) DEFAULT 0,
    warning_level ENUM('none','yellow','orange','red') DEFAULT 'none',
    final_rating ENUM('excellent','good','passed','need_monitoring','failed','not_enough_data') NULL,
    decision VARCHAR(255) NULL,
    eligibility_result ENUM('eligible','review','not_eligible') NULL,

    status ENUM('draft','waiting_approval','approved','rejected','closed') DEFAULT 'draft',
    created_by INT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,

    INDEX idx_period_staff (period_id, staff_id),
    INDEX idx_department (department_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Cấu hình KPI target
CREATE TABLE IF NOT EXISTS tbl_kpi_targets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    period_id INT NULL,
    department_id INT NULL,
    staff_id INT NULL,
    position_code VARCHAR(100) NULL,
    kpi_code VARCHAR(100) NOT NULL,
    kpi_name VARCHAR(255) NOT NULL,
    kpi_group ENUM('output','quality','deadline','process','reporting','bsc','okr') NOT NULL,
    target_value DECIMAL(15,2) DEFAULT 0,
    weight DECIMAL(10,2) DEFAULT 0,
    max_score DECIMAL(10,2) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,

    INDEX idx_period_department_staff (period_id, department_id, staff_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Chi tiết điểm từng KPI trong phiếu
CREATE TABLE IF NOT EXISTS tbl_kpi_form_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kpi_form_id INT NOT NULL,
    kpi_target_id INT NULL,
    kpi_code VARCHAR(100) NULL,
    kpi_name VARCHAR(255) NOT NULL,
    kpi_group VARCHAR(100) NULL,
    target_value DECIMAL(15,2) DEFAULT 0,
    actual_value DECIMAL(15,2) DEFAULT 0,
    achievement_rate DECIMAL(10,2) DEFAULT 0,
    weight DECIMAL(10,2) DEFAULT 0,
    raw_score DECIMAL(10,2) DEFAULT 0,
    final_score DECIMAL(10,2) DEFAULT 0,
    note TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,

    INDEX idx_kpi_form (kpi_form_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Vi phạm / lỗi KPI
CREATE TABLE IF NOT EXISTS tbl_kpi_violations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    period_id INT NOT NULL,
    staff_id INT NOT NULL,
    kpi_form_id INT NULL,
    task_id INT NULL,
    process_child_id INT NULL,
    violation_code VARCHAR(100) NOT NULL,
    violation_name VARCHAR(255) NOT NULL,
    severity ENUM('minor','major','critical') DEFAULT 'minor',
    penalty_score DECIMAL(10,2) DEFAULT 0,
    is_hard_fail TINYINT(1) DEFAULT 0,
    description TEXT NULL,
    evidence TEXT NULL,
    created_by INT NULL,
    created_at DATETIME NULL,

    INDEX idx_period_staff (period_id, staff_id),
    INDEX idx_task (task_id),
    INDEX idx_form (kpi_form_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Đóng góp / cải tiến
CREATE TABLE IF NOT EXISTS tbl_kpi_contributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    period_id INT NOT NULL,
    staff_id INT NOT NULL,
    kpi_form_id INT NULL,
    contribution_type ENUM('improvement','cost_saving','risk_reduction','support_team','training','other') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    bonus_score DECIMAL(10,2) DEFAULT 0,
    approved_score DECIMAL(10,2) DEFAULT 0,
    evidence TEXT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_by INT NULL,
    created_at DATETIME NULL,

    INDEX idx_period_staff (period_id, staff_id),
    INDEX idx_form (kpi_form_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Luồng duyệt KPI
CREATE TABLE IF NOT EXISTS tbl_kpi_approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kpi_form_id INT NOT NULL,
    step_order INT NOT NULL,
    approver_role ENUM('leader','hr','internal_audit','risk_control','bod') NOT NULL,
    approver_id INT NULL,
    status ENUM('waiting','approved','rejected','skipped') DEFAULT 'waiting',
    note TEXT NULL,
    approved_at DATETIME NULL,
    created_at DATETIME NULL,

    INDEX idx_kpi_form (kpi_form_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Audit log
CREATE TABLE IF NOT EXISTS tbl_kpi_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kpi_form_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    old_data LONGTEXT NULL,
    new_data LONGTEXT NULL,
    note TEXT NULL,
    created_by INT NULL,
    created_at DATETIME NULL,

    INDEX idx_kpi_form (kpi_form_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
