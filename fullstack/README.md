# 森码云实人认证系统 - 完整版

此目录包含基于 ThinkPHP 6 + Vue 3 + Vite 的完整堆栈版本。

## 与零依赖版本的区别

| 特性 | 零依赖版 (根目录) | 完整版 (fullstack/) |
|------|-----------------|-------------------|
| 部署方式 | 上传即用 | 需构建步骤 |
| 后端框架 | 纯PHP | ThinkPHP 6 |
| 前端构建 | 无 (CDN) | Vue 3 + Vite + npm |
| 开发体验 | 简单 | 完整工程化 |
| 功能完整度 | 核心功能 | 全部功能 |

## 安装步骤

```bash
# 1. 进入完整版目录
cd fullstack/

# 2. 安装PHP依赖
composer install

# 3. 安装前端依赖并构建
cd frontend/
npm install
npm run build

# 4. 将构建产物复制到public目录
cp -r dist/* ../public/

# 5. 域名绑定到 fullstack/public/ 目录
# 6. 访问 /install/ 完成安装
```

## 技术栈

- 后端: PHP 8.1+, ThinkPHP 6, GuzzleHTTP
- 前端: Vue 3, Vite, Vue Router, Pinia, Lucide Vue Next, ECharts, Animate.css
- 数据库: MySQL 5.7+

## 目录结构

```
fullstack/
├── app/                    # ThinkPHP 应用
│   ├── controller/         # 控制器
│   ├── middleware/         # 中间件
│   ├── model/              # 数据模型
│   └── service/            # 服务层
├── config/                 # 配置文件
├── frontend/               # Vue3 前端源码
├── public/                 # Web根目录
├── route/                  # 路由定义
├── composer.json
└── nginx/                  # Nginx配置
```

## 开源协议

MIT License