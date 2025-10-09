-- Rick's Golden Question Contest System - Complete Database Schema
-- Generated: October 8, 2025
-- Laravel 12 + MySQL

-- ====================
-- USERS & AUTHENTICATION
-- ====================

-- Users table (extends Laravel's default)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    birthdate DATE NOT NULL, -- Age verification
    is_admin BOOLEAN DEFAULT FALSE,
    show_name_publicly BOOLEAN DEFAULT FALSE, -- Privacy control
    total_winnings DECIMAL(10,2) DEFAULT 0,
    last_won_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_last_won (last_won_at),
    CONSTRAINT chk_age CHECK (TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 18)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,

    INDEX idx_user (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- PHASE 1: EXISTING BAG CODE SYSTEM
-- ====================

-- Trivia codes (from Phase 1 - keep existing)
CREATE TABLE trivia_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(4) NOT NULL UNIQUE,
    title VARCHAR(255),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    views_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_code (code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Answers for bag codes
CREATE TABLE answers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trivia_code_id BIGINT UNSIGNED NOT NULL,
    answer_text TEXT NOT NULL,
    order_position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (trivia_code_id) REFERENCES trivia_codes(id) ON DELETE CASCADE,
    INDEX idx_trivia_code (trivia_code_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Advertisement boxes
CREATE TABLE ad_boxes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(500),
    html_content TEXT,
    click_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_active (is_active),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Code views tracking
CREATE TABLE code_views (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trivia_code_id BIGINT UNSIGNED NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (trivia_code_id) REFERENCES trivia_codes(id) ON DELETE CASCADE,
    INDEX idx_code (trivia_code_id),
    INDEX idx_viewed (viewed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- PHASE 2: GOLDEN QUESTION CONTEST
-- ====================

-- Daily questions (Golden Question system)
CREATE TABLE daily_questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    option_a VARCHAR(500) NOT NULL,
    option_b VARCHAR(500) NOT NULL,
    option_c VARCHAR(500) NOT NULL,
    option_d VARCHAR(500) NOT NULL,
    correct_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
    explanation TEXT, -- "Did you know..." educational content
    prize_amount DECIMAL(8,2) DEFAULT 10.00,
    scheduled_for TIMESTAMP NOT NULL,
    winner_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT FALSE,
    submission_count INT DEFAULT 0,
    correct_submission_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (winner_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_scheduled (scheduled_for),
    INDEX idx_active (is_active),
    INDEX idx_winner (winner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Submissions (user answers)
CREATE TABLE submissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    daily_question_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    selected_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
    is_correct BOOLEAN NOT NULL,
    ip_address VARCHAR(45),
    device_fingerprint VARCHAR(255),
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    sticker_id BIGINT UNSIGNED NULL, -- Which sticker was scanned
    submitted_at TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6), -- Microsecond precision for tie-breaking
    random_tiebreaker INT UNSIGNED NOT NULL, -- Random 1-1000000 for tie-breaking
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (daily_question_id) REFERENCES daily_questions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (sticker_id) REFERENCES stickers(id) ON DELETE SET NULL,
    INDEX idx_question (daily_question_id),
    INDEX idx_user (user_id),
    INDEX idx_submitted (submitted_at),
    INDEX idx_correct (is_correct),
    INDEX idx_ip_date (ip_address, daily_question_id),
    INDEX idx_device_date (device_fingerprint, daily_question_id),
    UNIQUE KEY unique_user_question (user_id, daily_question_id) -- One submission per user per question
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Winners (first correct answer)
CREATE TABLE winners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    daily_question_id BIGINT UNSIGNED NOT NULL,
    submission_id BIGINT UNSIGNED NOT NULL,
    prize_amount DECIMAL(8,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (daily_question_id) REFERENCES daily_questions(id) ON DELETE CASCADE,
    FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_question (daily_question_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- LOCATION & QR TRACKING
-- ====================

-- Stickers (unique QR codes for each physical location)
CREATE TABLE stickers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unique_code VARCHAR(20) UNIQUE NOT NULL, -- abc123xyz
    location_name VARCHAR(255), -- "Oakwood Park Dog Station #3"
    property_name VARCHAR(255), -- "Oakwood Apartments"
    property_manager_id BIGINT UNSIGNED NULL, -- Future: multi-client tracking
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    installed_at DATE NULL,
    status ENUM('active', 'inactive', 'damaged', 'removed') DEFAULT 'active',
    scan_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_code (unique_code),
    INDEX idx_status (status),
    INDEX idx_property (property_manager_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sticker scans (tracking who scanned where)
CREATE TABLE sticker_scans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sticker_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL, -- If logged in
    ip_address VARCHAR(45),
    user_agent TEXT,
    scan_latitude DECIMAL(10, 8) NULL, -- User's location at scan
    scan_longitude DECIMAL(11, 8) NULL,
    scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (sticker_id) REFERENCES stickers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_sticker (sticker_id),
    INDEX idx_user (user_id),
    INDEX idx_scanned (scanned_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- GIFT CARDS & PRIZES
-- ====================

-- Gift cards (Tremendous API integration)
CREATE TABLE gift_cards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    winner_id BIGINT UNSIGNED NOT NULL,
    order_id VARCHAR(255) NOT NULL UNIQUE, -- Tremendous order ID
    reward_id VARCHAR(255) NOT NULL UNIQUE, -- Tremendous reward ID
    amount DECIMAL(8,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    status ENUM('pending', 'delivered', 'redeemed', 'failed') DEFAULT 'pending',
    redemption_link TEXT,
    delivery_method VARCHAR(50) DEFAULT 'EMAIL',
    delivered_at TIMESTAMP NULL,
    redeemed_at TIMESTAMP NULL,
    provider VARCHAR(50) DEFAULT 'tremendous',
    provider_response JSON,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (winner_id) REFERENCES winners(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_winner (winner_id),
    INDEX idx_status (status),
    INDEX idx_delivered (delivered_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gift card delivery logs (retry tracking)
CREATE TABLE gift_card_delivery_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gift_card_id BIGINT UNSIGNED NOT NULL,
    attempt_number INT DEFAULT 1,
    status ENUM('success', 'failed', 'pending_retry') NOT NULL,
    error_message TEXT NULL,
    api_response JSON,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (gift_card_id) REFERENCES gift_cards(id) ON DELETE CASCADE,
    INDEX idx_gift_card (gift_card_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- BUDGET & FINANCE
-- ====================

-- Prize pools (monthly budget tracking)
CREATE TABLE prize_pools (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    month DATE NOT NULL UNIQUE, -- 2025-10-01
    budget DECIMAL(10,2) DEFAULT 0,
    spent DECIMAL(10,2) DEFAULT 0,
    remaining DECIMAL(10,2) GENERATED ALWAYS AS (budget - spent) STORED,
    sponsor_id BIGINT UNSIGNED NULL, -- Future: Purina sponsorship
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_month (month),
    INDEX idx_sponsor (sponsor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Budget transactions (audit trail)
CREATE TABLE budget_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prize_pool_id BIGINT UNSIGNED NOT NULL,
    type ENUM('deposit', 'withdrawal', 'prize', 'refund') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255),
    reference_id BIGINT UNSIGNED NULL, -- gift_card_id if type='prize'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (prize_pool_id) REFERENCES prize_pools(id) ON DELETE CASCADE,
    INDEX idx_pool (prize_pool_id),
    INDEX idx_type (type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- NOTIFICATIONS
-- ====================

-- Notifications (Laravel's default table)
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_notifiable (notifiable_type, notifiable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- SECURITY & AUDIT
-- ====================

-- Admin audit logs
CREATE TABLE admin_audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(100) NOT NULL, -- 'created', 'updated', 'deleted'
    model_type VARCHAR(100) NOT NULL, -- 'DailyQuestion', 'Winner', etc.
    model_id BIGINT UNSIGNED,
    changes JSON, -- Before/after values
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (admin_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_admin (admin_user_id),
    INDEX idx_model (model_type, model_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed login attempts (security monitoring)
CREATE TABLE failed_login_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_ip (ip_address),
    INDEX idx_attempted (attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- QUEUE & JOBS
-- ====================

-- Jobs (Laravel queue)
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,

    INDEX idx_queue_reserved (queue, reserved_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed jobs
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- CACHE
-- ====================

-- Cache (Laravel cache driver)
CREATE TABLE cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,

    INDEX idx_expiration (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache locks
CREATE TABLE cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- ANALYTICS VIEWS (For Performance)
-- ====================

-- Daily analytics summary (materialized view concept)
CREATE TABLE daily_analytics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL UNIQUE,
    total_scans INT DEFAULT 0,
    total_submissions INT DEFAULT 0,
    total_winners INT DEFAULT 0,
    total_spent DECIMAL(10,2) DEFAULT 0,
    conversion_rate DECIMAL(5,2) DEFAULT 0, -- submissions/scans * 100
    avg_submissions_per_question DECIMAL(8,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================
-- SETTINGS & CONFIG
-- ====================

-- Settings (key-value store for app config)
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) UNIQUE NOT NULL,
    value TEXT,
    type VARCHAR(50) DEFAULT 'string', -- string, boolean, integer, json
    description VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO settings (`key`, value, type, description) VALUES
('contest_active', 'true', 'boolean', 'Master switch for contest system'),
('question_rotation_start_hour', '10', 'integer', 'Earliest hour for question rotation (24h format)'),
('question_rotation_end_hour', '20', 'integer', 'Latest hour for question rotation (24h format)'),
('require_geolocation', 'false', 'boolean', 'Require geolocation for submissions'),
('max_daily_submissions_per_ip', '3', 'integer', 'Max submissions per IP per day (across all questions)'),
('enable_captcha', 'true', 'boolean', 'Enable CAPTCHA on answer submission'),
('admin_alert_email', 'rick@example.com', 'string', 'Email for admin alerts'),
('admin_alert_sms', '', 'string', 'Phone number for SMS alerts (optional)'),
('low_budget_threshold', '100', 'integer', 'Alert when prize pool remaining falls below this amount'),
('maintenance_mode', 'false', 'boolean', 'Put contest in maintenance mode');
