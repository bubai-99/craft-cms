-- Menu Manager Plugin Database Migration
-- Run this in phpMyAdmin or your database tool

CREATE TABLE IF NOT EXISTS `menumanager_menus` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `handle` varchar(255) NOT NULL,
    `structure` mediumtext,
    `maxLevels` int(11) DEFAULT NULL,
    `dateCreated` datetime NOT NULL,
    `dateUpdated` datetime NOT NULL,
    `uid` char(36) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_menumanager_menus_handle` (`handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;