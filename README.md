# 森码云实人认证系统

企业级人机验证/实人认证平台，基于 Vue3 + PHP 构建，对接魔方财务系统。

**域名**: face.builds.codes

## 功能特性

- **多源人脸识别**: 支持腾讯云慧眼、百度智能云、支付宝、聚合数据、阿里云市场等 6 种接口，主备自动切换
- **自研活体检测**: 内置动作序列分析、光流变化检测、摩尔纹检测、翻拍检测
- **身份证校验**: 基于 GB/T 2260 行政区划码表和 ISO 7064:1983 MOD 11-2 校验算法
- **魔方财务对接**: 提供完整 certification 类型插件，Token 安全机制
- **人工审核队列**: 自动认证失败转入人工审核
- **完整后台管理**: 控制台、接口管理、认证记录、审核、Token管理、系统设置
- **安全合规**: 符合《个人信息保护法》，数据加密存储，审计日志

## 技术栈

| 层级 | 技术 |
|------|------|
| 前端 | Vue 3, Vite, Vue Router, Pinia, Lucide Icons, ECharts, Animate.css |
| 后端 | PHP 8.1+, ThinkPHP 6 |
| 数据库 | MySQL 5.7+ / MariaDB 10.3+ |
| HTTP | GuzzleHTTP, Axios |
| 安全 | AES-256-GCM, BCrypt, CSRF, SQL预处理 |

## 安装教程

### 环境要求

- PHP >= 8.1
- MySQL >= 5.7 或 MariaDB >= 10.3
- Apache（mod_rewrite）或 Nginx
- PHP 扩展: curl, openssl, gd, fileinfo, mbstring, pdo, pdo_mysql

### 安装步骤

1. **克隆项目**
```bash
git clone https://github.com/reuei/senmaoyun-face-auth.git
cd senmaoyun-face-auth
```

2. **安装 PHP 依赖**
```bash
composer install
```

3. **安装前端依赖并构建**
```bash
cd frontend
npm install
npm run build
cd ..
```

4. **配置虚拟主机**
   - 将域名绑定到 `public/` 目录
   - Apache: 使用 `public/.htaccess`
   - Nginx: 参考 `nginx/face.builds.codes.conf`

5. **访问安装向导**
   - 打开 `https://face.builds.codes/install/`
   - 按步骤完成：环境检测 → 数据库配置 → 管理员创建 → 接口配置

6. **配置人脸识别接口**
   - 登录后台 `https://face.builds.codes/admin/login`
   - 进入「接口管理」配置各接口 API 密钥

## 魔方财务对接

### 插件安装

1. 下载插件：后台「插件中心」→ 下载插件
2. 解压到魔方财务系统：`/public/plugins/certification/senmaoyun/`
3. 在魔方财务后台启用插件
4. 配置：
   - 系统地址：`https://face.builds.codes`
   - API Key：在森码云后台生成

### 认证流程

```
魔方财务用户发起认证
    ↓
魔方财务调用森码云生成Token
    ↓
用户跳转至 face.builds.codes/verify?token=xxx
    ↓
同意协议 → 身份录入 → 人脸识别 → 结果返回
    ↓
森码云回调魔方财务，更新用户实名状态
```

### Token 安全机制

- 请求 Token: 64位 SHA-256 随机字符串，5分钟有效期，单次使用
- 回调 Token: 同样机制，含签名校验防重放
- 人脸识别仅允许从魔方财务入口进入

## 目录结构

```
senmaoyun/
├── app/                    # PHP 应用
│   ├── controller/         # 控制器
│   │   ├── admin/          # 后台管理
│   │   ├── api/            # API 接口
│   │   └── install/        # 安装向导
│   ├── middleware/         # 中间件
│   ├── model/              # 数据模型
│   └── service/            # 服务层
│       └── face/           # 人脸识别驱动
├── config/                 # 配置文件
├── database/               # 数据库
│   └── install.sql         # 安装SQL
├── frontend/               # Vue3 前端源码
│   └── src/
│       ├── views/          # 页面组件
│       ├── stores/         # 状态管理
│       ├── router/         # 路由
│       └── utils/          # 工具函数
├── plugin/                 # 魔方财务插件
│   └── senmaoyun/
├── public/                 # Web 根目录
│   ├── index.php
│   └── .htaccess
├── nginx/                  # Nginx 配置
├── route/                  # 路由定义
├── bind-public.php         # 目录绑定提示
├── composer.json
├── .gitignore
└── README.md
```

## 开源协议

MIT License

## 开源组件致谢

- [Vue.js](https://vuejs.org/) - MIT
- [ThinkPHP](https://www.thinkphp.cn/) - Apache 2.0
- [Lucide Icons](https://lucide.dev/) - ISC
- [ECharts](https://echarts.apache.org/) - Apache 2.0
- [Animate.css](https://animate.style/) - MIT
- [GuzzleHTTP](https://docs.guzzlephp.org/) - MIT
- [腾讯云SDK](https://github.com/TencentCloud/tencentcloud-sdk-php) - Apache 2.0