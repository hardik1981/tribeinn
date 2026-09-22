-- MySQL 8 / MariaDB 10.2+. Run in the selected application database.
CREATE TABLE IF NOT EXISTS units (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(180) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Occupied nights are [start_date, end_date): start INCLUSIVE, end EXCLUSIVE.
-- Oct 3 -> Oct 7 occupies Oct 3,4,5,6. Adjacent stays are allowed.
-- Source is extensible; only 'manual' can be changed by this admin module.
CREATE TABLE IF NOT EXISTS availability_blocks (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    unit_id BIGINT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason VARCHAR(500) NOT NULL DEFAULT '',
    source VARCHAR(40) NOT NULL DEFAULT 'manual',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_availability_unit FOREIGN KEY (unit_id) REFERENCES units(id),
    CONSTRAINT ck_availability_range CHECK (end_date > start_date),
    INDEX availability_lookup (unit_id, end_date, start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO units (slug, name) VALUES ('the-beginning', 'The Beginning')
ON DUPLICATE KEY UPDATE slug = VALUES(slug);
