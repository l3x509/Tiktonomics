-- ============================================================
--  Tiktonomics Database Schema
--  Import via: Hostinger hPanel → Databases → phpMyAdmin
--  Compatible with: MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── Co-host Directory ──────────────────────────────────────
-- Stores creator listings for the public co-host finder.
-- Shared across all users (the whole point of the feature).

CREATE TABLE IF NOT EXISTS `cohosts` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `handle`       VARCHAR(100)    NOT NULL,
  `followers`    INT UNSIGNED    NOT NULL DEFAULT 0,
  `niche`        VARCHAR(100)    NOT NULL DEFAULT 'General / Lifestyle',
  `timezone`     VARCHAR(50)     NOT NULL DEFAULT 'EST (UTC-5)',
  `availability` VARCHAR(200)    NOT NULL DEFAULT 'Open',
  `created_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_niche`    (`niche`),
  INDEX `idx_created`  (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── Global Leaderboard ─────────────────────────────────────
-- Optional: creators can submit a battle result to appear on
-- a global leaderboard. Handle is displayed publicly.

CREATE TABLE IF NOT EXISTS `leaderboard` (
  `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `handle`      VARCHAR(100)    NOT NULL DEFAULT 'Anonymous',
  `coins`       INT UNSIGNED    NOT NULL,
  `earned`      DECIMAL(10,2)   NOT NULL,
  `opponent`    VARCHAR(100)    NOT NULL DEFAULT 'Unknown',
  `result`      ENUM('win','loss') NOT NULL DEFAULT 'win',
  `battle_date` DATE            NOT NULL,
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_earned`   (`earned` DESC),
  INDEX `idx_handle`   (`handle`),
  INDEX `idx_created`  (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── Sample data (delete after testing) ────────────────────
INSERT INTO `cohosts` (`handle`, `followers`, `niche`, `timezone`, `availability`) VALUES
  ('@tiktok_creator1', 52000, 'General / Lifestyle', 'EST (UTC-5)', 'Weekday evenings'),
  ('@battlequeen99',   12000, 'Beauty / Fashion',    'PST (UTC-8)', 'Weekends'),
  ('@livegamer_x',     89000, 'Gaming',              'CST (UTC-6)', 'Daily 6-10pm');

INSERT INTO `leaderboard` (`handle`, `coins`, `earned`, `opponent`, `result`, `battle_date`) VALUES
  ('@champion_creator', 250000, 156.25, '@rival1', 'win',  CURDATE()),
  ('@battleking_live',  180000, 112.50, '@rival2', 'win',  CURDATE()),
  ('@gifted_streamer',   95000,  59.38, '@rival3', 'loss', CURDATE());

SET FOREIGN_KEY_CHECKS = 1;
