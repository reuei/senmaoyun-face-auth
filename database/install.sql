-- 森码云实人认证系统 - 数据库安装脚本
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00";
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `{prefix}admin` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nickname` varchar(64) DEFAULT '',
  `email` varchar(128) DEFAULT '',
  `avatar` varchar(255) DEFAULT '',
  `role` enum('super','admin','auditor') NOT NULL DEFAULT 'admin',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_ip` varchar(45) DEFAULT '',
  `last_login_time` datetime DEFAULT NULL,
  `login_count` int UNSIGNED NOT NULL DEFAULT 0,
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{prefix}face_driver` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `driver_code` varchar(32) NOT NULL,
  `driver_name` varchar(64) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `config` text,
  `sort` int NOT NULL DEFAULT 0,
  `last_test_time` datetime DEFAULT NULL,
  `last_test_result` tinyint(1) DEFAULT NULL,
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_driver_code` (`driver_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{prefix}certify_record` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `record_no` varchar(32) NOT NULL,
  `user_id` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `id_card` varchar(255) NOT NULL,
  `gender` enum('male','female','unknown') NOT NULL DEFAULT 'unknown',
  `birth_date` date DEFAULT NULL,
  `driver_code` varchar(32) NOT NULL DEFAULT '',
  `liveness_score` decimal(5,2) DEFAULT NULL,
  `compare_score` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','processing','success','failed','auditing') NOT NULL DEFAULT 'pending',
  `fail_reason` varchar(255) DEFAULT '',
  `retry_count` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `face_image` varchar(255) DEFAULT '',
  `action_video` varchar(255) DEFAULT '',
  `certify_time` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT '',
  `user_agent` varchar(500) DEFAULT '',
  `callback_status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `callback_response` text,
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_record_no` (`record_no`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{prefix}certify_token` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` varchar(128) NOT NULL,
  `type` enum('request','callback') NOT NULL DEFAULT 'request',
  `user_id` varchar(64) NOT NULL DEFAULT '',
  `callback_url` varchar(500) DEFAULT '',
  `expire_time` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `used_time` datetime DEFAULT NULL,
  `record_id` bigint UNSIGNED DEFAULT NULL,
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expire_time` (`expire_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{prefix}setting` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL,
  `value` text,
  `type` enum('string','text','number','bool','json') NOT NULL DEFAULT 'string',
  `group` varchar(32) NOT NULL DEFAULT 'system',
  `title` varchar(64) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{prefix}audit_log` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int UNSIGNED DEFAULT NULL,
  `action` varchar(64) NOT NULL,
  `module` varchar(32) NOT NULL DEFAULT '',
  `target_type` varchar(32) DEFAULT '',
  `target_id` varchar(64) DEFAULT '',
  `content` text,
  `ip_address` varchar(45) DEFAULT '',
  `user_agent` varchar(500) DEFAULT '',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_action` (`action`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `{prefix}rate_limit` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `action` varchar(32) NOT NULL,
  `count` int UNSIGNED NOT NULL DEFAULT 1,
  `window_start` datetime NOT NULL,
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_action` (`ip_address`,`action`),
  KEY `idx_window_start` (`window_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `{prefix}face_driver` (`driver_code`, `driver_name`, `enabled`, `is_default`, `config`, `sort`) VALUES
('self', '自研活体检测', 1, 1, '{}', 0),
('tencent', '腾讯云慧眼', 0, 0, '{"secret_id":"","secret_key":"","region":"ap-guangzhou"}', 1),
('baidu', '百度智能云人脸识别', 0, 0, '{"api_key":"","secret_key":"","app_id":""}', 2),
('alipay', '支付宝活体检测', 0, 0, '{"app_id":"","private_key":"","alipay_public_key":""}', 3),
('juhe', '聚合数据活体检测', 0, 0, '{"api_key":""}', 4),
('aliyun_market', '阿里云市场活体检测', 0, 0, '{"app_code":""}', 5);

INSERT INTO `{prefix}setting` (`key`, `value`, `type`, `group`, `title`) VALUES
('site_name', '森码云实人认证系统', 'string', 'system', '站点名称'),
('site_domain', 'face.builds.codes', 'string', 'system', '站点域名'),
('icp_number', '', 'string', 'system', '备案号'),
('mofang_url', '', 'string', 'system', '魔方财务系统地址'),
('max_retry', '3', 'number', 'face', '最大重试次数'),
('liveness_threshold', '80', 'number', 'face', '活体检测阈值'),
('compare_threshold', '80', 'number', 'face', '人脸比对阈值'),
('data_retention', '24', 'number', 'face', '数据保留时间(小时)'),
('rate_limit', '10', 'number', 'security', '速率限制(次/分钟)');

COMMIT;