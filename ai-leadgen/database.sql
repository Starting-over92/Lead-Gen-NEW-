CREATE DATABASE IF NOT EXISTS ai_leadgen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ai_leadgen;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    niche VARCHAR(255) NOT NULL,
    country VARCHAR(100) NOT NULL,
    city VARCHAR(100) NULL,
    company_size VARCHAR(50) NOT NULL,
    job_titles TEXT NOT NULL,
    outreach_tone VARCHAR(50) NOT NULL,
    daily_limit INT NOT NULL,
    status ENUM('active', 'paused') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_niche (niche),
    INDEX idx_country (country),
    INDEX idx_city (city),
    INDEX idx_status (status)
) ENGINE=InnoDB;

INSERT INTO campaigns (niche, country, city, company_size, job_titles, outreach_tone, daily_limit, status) VALUES
('Real Estate Agents', 'UAE', 'Dubai', '11-50', '["Founder","CEO"]', 'Friendly', 50, 'active'),
('Software Companies', 'USA', NULL, '51-200', '["CTO","Product Manager","Engineering Manager"]', 'Formal', 120, 'active'),
('Healthcare Clinics', 'Canada', 'Toronto', '201-500', '["Operations Manager","HR Manager"]', 'Aggressive', 80, 'paused');
