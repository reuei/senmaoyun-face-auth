<?php
/**
 * 自研活体检测驱动
 * 不依赖第三方API，基于GD库图像分析
 */
class SelfDriver
{
    public function detectLiveness($imageBase64, $actionFrames = [], $options = [])
    {
        $scores = [];
        $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageBase64));
        if (!$imgData) return ['success' => false, 'liveness_score' => 0, 'message' => '图片数据无效'];

        $img = @imagecreatefromstring($imgData);
        if (!$img) return ['success' => false, 'liveness_score' => 0, 'message' => '无法解析图片'];

        $w = imagesx($img); $h = imagesy($img);

        // 1. 分辨率检测
        if ($w < 240 || $h < 240) { imagedestroy($img); return ['success' => false, 'liveness_score' => 20, 'message' => '图片分辨率过低']; }

        // 2. 亮度检测
        $brightness = 0; $cnt = 0;
        for ($x = 0; $x < $w; $x += 8) {
            for ($y = 0; $y < $h; $y += 8) {
                $rgb = imagecolorat($img, $x, $y);
                $brightness += 0.299 * (($rgb >> 16) & 0xFF) + 0.587 * (($rgb >> 8) & 0xFF) + 0.114 * ($rgb & 0xFF);
                $cnt++;
            }
        }
        $avgBrightness = $cnt > 0 ? $brightness / $cnt : 0;
        if ($avgBrightness < 40) { imagedestroy($img); return ['success' => false, 'liveness_score' => 30, 'message' => '光线不足，请确保面部光线充足']; }
        if ($avgBrightness > 235) { imagedestroy($img); return ['success' => false, 'liveness_score' => 40, 'message' => '光线过强，请避免强光直射']; }
        $scores['brightness'] = $avgBrightness > 80 && $avgBrightness < 200 ? 90 : 70;

        // 3. 纹理复杂度检测（翻拍检测）
        $highFreq = 0; $total = 0;
        for ($x = 0; $x < $w - 4; $x += 4) {
            for ($y = 0; $y < $h - 4; $y += 4) {
                $b1 = imagecolorat($img, $x, $y) & 0xFF;
                $b2 = imagecolorat($img, $x + 4, $y) & 0xFF;
                if (abs($b1 - $b2) > 25) $highFreq++;
                $total++;
            }
        }
        $textureRatio = $total > 0 ? $highFreq / $total : 0;
        $scores['texture'] = $textureRatio > 0.35 ? 50 : ($textureRatio > 0.2 ? 75 : 90);
        imagedestroy($img);

        // 综合评分
        $totalScore = ($scores['brightness'] * 0.4 + $scores['texture'] * 0.6);
        $passed = $totalScore >= 75;

        return [
            'success' => $passed,
            'liveness_score' => round($totalScore, 1),
            'message' => $passed ? '活体检测通过' : '活体检测未通过，请确保是真人操作',
            'details' => $scores,
        ];
    }
}