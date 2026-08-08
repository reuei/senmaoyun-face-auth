-- ============================================================
-- 森码云实人认证系统 - 数据库安装脚本
-- 版本: 1.0.0
-- 日期: 2026-08-08
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00";
SET NAMES utf8mb4;

-- -----------------------------------------------------------
-- 表结构: 管理员表
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `{prefix}admin` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码（bcrypt）',
  `nickname` varchar(64) DEFAULT '' COMMENT '昵称',
  `email` varchar(128) DEFAULT '' COMMENT '邮箱',
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `role` enum('super','admin','auditor') NOT NULL DEFAULT 'admin' COMMENT '角色',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1启用 0禁用',
  `last_login_ip` varchar(45) DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `login_count` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '登录次数',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

-- -----------------------------------------------------------
-- 表结构: 接口驱动配置表
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `{prefix}face_driver` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `driver_code` varchar(32) NOT NULL COMMENT '驱动代码',
  `driver_name` varchar(64) NOT NULL COMMENT '驱动名称',
  `enabled` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否启用',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否默认',
  `config` text COMMENT '配置（加密存储）',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `last_test_time` datetime DEFAULT NULL COMMENT '最后测试时间',
  `last_test_result` tinyint(1) DEFAULT NULL COMMENT '最后测试结果',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_driver_code` (`driver_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='人脸识别接口驱动表';

-- -----------------------------------------------------------
-- 表结构: 认证记录表
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `{prefix}certify_record` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `record_no` varchar(32) NOT NULL COMMENT '记录编号',
  `user_id` varchar(64) NOT NULL DEFAULT '' COMMENT '用户标识（魔方财务用户ID）',
  `name` varchar(64) NOT NULL COMMENT '姓名（加密存储）',
  `id_card` varchar(255) NOT NULL COMMENT '身份证号（加密存储）',
  `gender` enum('male','female','unknown') NOT NULL DEFAULT 'unknown' COMMENT '性别',
  `birth_date` date DEFAULT NULL COMMENT '出生日期',
  `driver_code` varchar(32) NOT NULL DEFAULT '' COMMENT '使用的接口驱动',
  `liveness_score` decimal(5,2) DEFAULT NULL COMMENT '活体检测分数',
  `compare_score` decimal(5,2) DEFAULT NULL COMMENT '人脸比对分数',
  `status` enum('pending','processing','success','failed','auditing') NOT NULL DEFAULT 'pending' COMMENT '状态',
  `fail_reason` varchar(255) DEFAULT '' COMMENT '失败原因',
  `retry_count` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '重试次数',
  `face_image` varchar(255) DEFAULT '' COMMENT '采集人脸图片路径',
  `action_video` varchar(255) DEFAULT '' COMMENT '动作视频路径',
  `certify_time` datetime DEFAULT NULL COMMENT '认证完成时间',
  `ip_address` varchar(45) DEFAULT '' COMMENT 'IP地址',
  `user_agent` varchar(500) DEFAULT '' COMMENT 'User Agent',
  `callback_status` enum('pending','success','failed') NOT NULL DEFAULT 'pending' COMMENT '回调状态',
  `callback_response` text COMMENT '回调响应',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_record_no` (`record_no`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='认证记录表';

-- -----------------------------------------------------------
-- 表结构: 认证Token表
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `{prefix}certify_token` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` varchar(128) NOT NULL COMMENT 'Token值',
  `type` enum('request','callback') NOT NULL DEFAULT 'request' COMMENT '类型',
  `user_id` varchar(64) NOT NULL DEFAULT '' COMMENT '用户标识',
  `callback_url` varchar(500) DEFAULT '' COMMENT '回调地址',
  `expire_time` datetime NOT NULL COMMENT '过期时间',
  `used` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已使用',
  `used_time` datetime DEFAULT NULL COMMENT '使用时间',
  `record_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '关联认证记录ID',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_token` (`token`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expire_time` (`expire_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='认证Token表';

-- -----------------------------------------------------------
-- 表结构: 系统配置表
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `{prefix}setting` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL COMMENT '配置键',
  `value` text COMMENT '配置值',
  `type` enum('string','text','number','bool','json') NOT NULL DEFAULT 'string' COMMENT '值类型',
  `group` varchar(32) NOT NULL DEFAULT 'system' COMMENT '分组',
  `title` varchar(64) DEFAULT '' COMMENT '标题',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';

-- -----------------------------------------------------------
-- 表结构: 审计日志表
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `{prefix}audit_log` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) UNSIGNED DEFAULT NULL COMMENT '管理员ID',
  `action` varchar(64) NOT NULL COMMENT '操作名称',
  `module` varchar(32) NOT NULL DEFAULT '' COMMENT '模块',
  `target_type` varchar(32) DEFAULT '' COMMENT '目标类型',
  `target_id` varchar(64) DEFAULT '' COMMENT '目标ID',
  `content` text COMMENT '操作详情（JSON）',
  `ip_address` varchar(45) DEFAULT '' COMMENT 'IP地址',
  `user_agent` varchar(500) DEFAULT '' COMMENT 'User Agent',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin_id` (`admin_id`),
  KEY `idx_action` (`action`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='审计日志表';

-- -----------------------------------------------------------
-- 表结构: 速率限制表
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `{prefix}rate_limit` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL COMMENT 'IP地址',
  `action` varchar(32) NOT NULL COMMENT '操作类型',
  `count` int(11) UNSIGNED NOT NULL DEFAULT 1 COMMENT '计数',
  `window_start` datetime NOT NULL COMMENT '窗口起始时间',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ip_action` (`ip_address`, `action`),
  KEY `idx_window_start` (`window_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='速率限制表';

-- -----------------------------------------------------------
-- 插入默认数据
-- -----------------------------------------------------------

-- 默认接口驱动
INSERT INTO `{prefix}face_driver` (`driver_code`, `driver_name`, `enabled`, `is_default`, `config`, `sort`) VALUES
('self', '自研活体检测', 1, 1, '{}', 0),
('tencent', '腾讯云慧眼', 0, 0, '{"secret_id":"","secret_key":"","region":"ap-guangzhou"}', 1),
('baidu', '百度智能云人脸识别', 0, 0, '{"api_key":"","secret_key":"","app_id":""}', 2),
('alipay', '支付宝活体检测', 0, 0, '{"app_id":"","private_key":"","alipay_public_key":""}', 3),
('juhe', '聚合数据活体检测', 0, 0, '{"api_key":""}', 4),
('aliyun_market', '阿里云市场活体检测', 0, 0, '{"app_code":""}', 5);

-- 默认系统配置
INSERT INTO `{prefix}setting` (`key`, `value`, `type`, `group`, `title`) VALUES
('site_name', '森码云实人认证系统', 'string', 'system', '站点名称'),
('site_domain', 'face.builds.codes', 'string', 'system', '站点域名'),
('icp_number', '', 'string', 'system', '备案号'),
('mofang_url', '', 'string', 'system', '魔方财务系统地址'),
('max_retry', '3', 'number', 'face', '最大重试次数'),
('liveness_threshold', '80', 'number', 'face', '活体检测阈值'),
('compare_threshold', '80', 'number', 'face', '人脸比对阈值'),
('data_retention', '24', 'number', 'face', '数据保留时间(小时)'),
('rate_limit', '10', 'number', 'security', '速率限制(次/分钟)'),
('face_encryption_key', '', 'string', 'security', '数据加密密钥'),
('agreement_content', '', 'text', 'content', '实人认证服务协议'),
('privacy_policy', '', 'text', 'content', '隐私政策'),
('face_auth_letter', '', 'text', 'content', '人脸识别授权书');

COMMIT;