<?php
/**
 * 后台登录处理（兼容POST到 /admin/login.php 的请求）
 * 由 admin/login.php 直接处理POST，此文件为兼容路由
 */
require __DIR__ . '/login.php';