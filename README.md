# 森码云实人认证系统

企业级人机验证/实人认证平台，提供**两套方案**可选。

**域名**: face.builds.codes

## 两套方案

| 方案 | 目录 | 说明 |
|------|------|------|
| **零依赖版** (推荐) | 根目录 `public/` | 上传即用，无需任何构建工具 |
| **完整版** | `fullstack/` | ThinkPHP 6 + Vue 3 + Vite，需 composer/npm |

## 特点

- **零依赖**: 无需 composer、npm、任何构建工具，上传到虚拟主机即可运行
- **多源接口**: 支持腾讯云慧眼、百度智能云、支付宝、聚合数据、阿里云市场 + 自研活体检测
- **自研算法**: 内置图像质量检测、亮度分析、纹理复杂度分析（翻拍检测）
- **魔方财务对接**: 提供完整 certification 类型插件，Token 安全机制
- **身份证校验**: ISO 7064:1983 MOD 11-2 校验算法
- **四步认证**: 协议签署 → 身份录入 → 人脸识别 → 结果返回
- **完整后台**: 控制台、接口管理、认证记录、人工审核、Token管理、系统设置、插件中心

## 技术栈

| 层级 | 技术 |
|------|------|
| 后端 | PHP 8.1+，纯 PHP，无框架依赖 |
| 数据库 | MySQL 5.7+ / MariaDB 10.3+ |
| 前端 | Vue 3 (CDN), Lucide Icons (CDN), ECharts (CDN) |
| 安全 | AES-256-GCM, BCrypt, CSRF, SQL预处理 |

## 安装（3步）

**前提**: PHP >= 8.1，MySQL >= 5.7，Apache( mod_rewrite )或 Nginx

### 1. 上传文件

将项目所有文件上传到虚拟主机，**域名绑定到 `public/` 目录**。

### 2. 访问安装向导

```
https://face.builds.codes/install.php
```

按提示完成：环境检测 → 数据库配置 → 管理员创建

### 3. 完成

安装成功后访问 `/admin/` 登录管理后台，配置人脸识别接口密钥。

## 目录结构

```
senmaoyun/
├── public/                  # Web根目录（域名绑定此目录）
│   ├── index.php            # 入口路由
│   ├── .htaccess            # Apache伪静态
│   ├── install.php          # 安装向导
│   ├── home.php             # 首页
│   ├── verify.php           # 认证页面（4步流程）
│   ├── forbidden.php        # 访问受限页
│   ├── bind-public.php      # 目录绑定提示
│   ├── admin/               # 管理后台
│   │   ├── index.php        # 后台路由
│   │   ├── login.php        # 登录页
│   │   ├── dashboard.php    # 控制台
│   │   ├── driver.php       # 接口管理
│   │   ├── record.php       # 认证记录
│   │   ├── audit.php        # 人工审核
│   │   ├── token.php        # Token管理
│   │   ├── setting.php      # 系统设置
│   │   └── plugin.php       # 插件中心
│   └── api/
│       └── index.php        # API路由
├── includes/                # 核心库
│   ├── config.php           # 配置
│   ├── database.php         # 数据库操作
│   ├── functions.php        # 通用函数
│   ├── encrypt.php          # 加密服务
│   ├── idcard.php           # 身份证校验
│   └── face/                # 人脸识别驱动
│       ├── self.php         # 自研活体检测
│       ├── tencent.php      # 腾讯云慧眼
│       ├── baidu.php        # 百度智能云
│       ├── alipay.php       # 支付宝
│       ├── juhe.php         # 聚合数据
│       └── aliyun.php       # 阿里云市场
├── database/
│   └── install.sql          # 数据库安装脚本
├── plugin-senmaoyun/        # 魔方财务插件
│   └── SenmaoyunPlugin.php
├── .env.example             # 环境变量示例
├── README.md
└── LICENSE
```

## 魔方财务对接

1. 下载插件：后台「插件中心」→ 下载插件
2. 放置到魔方财务系统：`/public/plugins/certification/senmaoyun/`
3. 在魔方财务后台启用插件
4. 配置：
   - 系统地址：`https://face.builds.codes`
   - API Key：在森码云后台查看（安装时自动生成）

## 接口说明

| 接口 | 代码 | 需要密钥 | 说明 |
|------|------|---------|------|
| 自研活体检测 | self | 否 | 默认启用，基于GD库图像分析 |
| 腾讯云慧眼 | tencent | SecretId/SecretKey | 需开通腾讯云人脸核身服务 |
| 百度智能云 | baidu | API Key/Secret Key | 需开通百度人脸识别服务 |
| 支付宝 | alipay | AppId/私钥 | 需开通支付宝开放平台 |
| 聚合数据 | juhe | API Key | 需注册聚合数据 |
| 阿里云市场 | aliyun_market | AppCode | 需在阿里云市场订阅 |

## 开源协议

MIT License