<?php
/**
 * 森码云实人认证系统 - 目录绑定提示页
 * 当访问根目录未正确绑定到 public/ 目录时显示此页面
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>目录绑定提示 - 森码云实人认证系统</title>
    <style>
        :root {
            --primary: #4F46E5;
            --primary-light: #EEF2FF;
            --text: #1F2937;
            --text-secondary: #6B7280;
            --bg: #F9FAFB;
            --card-bg: #FFFFFF;
            --border: #E5E7EB;
            --success: #10B981;
            --radius: 12px;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 25px rgba(0,0,0,0.08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans SC", sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
        }
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            padding: 48px 40px;
            max-width: 560px;
            width: 100%;
            text-align: center;
            border: 1px solid var(--border);
        }
        .illustration {
            width: 180px;
            height: 180px;
            margin: 0 auto 32px;
        }
        .illustration svg {
            width: 100%;
            height: 100%;
        }
        .icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #FEF3C7;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .icon-circle svg {
            width: 36px;
            height: 36px;
            color: #D97706;
        }
        h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }
        p {
            color: var(--text-secondary);
            font-size: 15px;
            margin-bottom: 8px;
        }
        .code-block {
            background: #1F2937;
            color: #E5E7EB;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 24px 0;
            text-align: left;
            font-family: "SF Mono", "Fira Code", "Fira Mono", "Roboto Mono", monospace;
            font-size: 13px;
            line-height: 1.8;
            overflow-x: auto;
            position: relative;
        }
        .code-block .comment { color: #6B7280; }
        .code-block .path { color: #FBBF24; }
        .code-block .cmd { color: #60A5FA; }
        .steps {
            text-align: left;
            margin: 24px 0;
            padding: 0;
            list-style: none;
        }
        .steps li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            color: var(--text-secondary);
        }
        .steps li:last-child { border-bottom: none; }
        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .footer-note {
            font-size: 12px;
            color: #9CA3AF;
            margin-top: 24px;
        }
        @media (max-width: 480px) {
            .card { padding: 32px 24px; }
            .code-block { font-size: 11px; padding: 12px 14px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>

        <h1>请将域名绑定到 public 目录</h1>
        <p>森码云实人认证系统的 Web 根目录应指向 <strong>public</strong> 文件夹</p>
        <p>当前检测到域名未正确绑定，请按以下步骤操作：</p>

        <div class="code-block">
            <span class="comment"># 当前项目根目录</span><br>
            <span class="path">/www/wwwroot/face.builds.codes</span><br><br>
            <span class="comment"># 正确配置示例（宝塔面板）</span><br>
            <span class="cmd">网站 → 设置 → 网站目录 → 运行目录</span><br>
            <span class="path">/www/wwwroot/face.builds.codes/public</span>
        </div>

        <ol class="steps">
            <li>
                <span class="step-num">1</span>
                <span>登录服务器管理面板（如宝塔面板、cPanel、DirectAdmin 等）</span>
            </li>
            <li>
                <span class="step-num">2</span>
                <span>找到域名 <strong>face.builds.codes</strong> 对应的网站设置</span>
            </li>
            <li>
                <span class="step-num">3</span>
                <span>将「网站目录」或「运行目录」设置为 <strong>public</strong> 文件夹</span>
            </li>
            <li>
                <span class="step-num">4</span>
                <span>保存配置后刷新本页面</span>
            </li>
        </ol>

        <p class="footer-note">
            森码云实人认证系统 &copy; <?php echo date('Y'); ?> &mdash; face.builds.codes
        </p>
    </div>
</body>
</html>