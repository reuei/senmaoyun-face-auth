<?php
namespace extend\face;

class SelfDriver
{
    public function detectLiveness($imageBase64, $actionFrames = [], $options = [])
    {
        $imgData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageBase64));
        if (!$imgData) return ['success' => false, 'liveness_score' => 0, 'message' => '图片数据无效'];
        $img = @imagecreatefromstring($imgData);
        if (!$img) return ['success' => false, 'liveness_score' => 0, 'message' => '无法解析图片'];

        $w = imagesx($img); $h = imagesy($img);
        if ($w < 240 || $h < 240) { imagedestroy($img); return ['success' => false, 'liveness_score' => 20, 'message' => '图片分辨率过低']; }

        $brightness = 0; $cnt = 0;
        for ($x = 0; $x < $w; $x += 8) {
            for ($y = 0; $y < $h; $y += 8) {
                $rgb = imagecolorat($img, $x, $y);
                $brightness += 0.299 * (($rgb >> 16) & 0xFF) + 0.587 * (($rgb >> 8) & 0xFF) + 0.114 * ($rgb & 0xFF);
                $cnt++;
            }
        }
        $avgBrightness = $cnt > 0 ? $brightness / $cnt : 0;
        if ($avgBrightness < 40) { imagedestroy($img); return ['success' => false, 'liveness_score' => 30, 'message' => '光线不足']; }
        if ($avgBrightness > 235) { imagedestroy($img); return ['success' => false, 'liveness_score' => 40, 'message' => '光线过强']; }
        $scores['brightness'] = $avgBrightness > 80 && $avgBrightness < 200 ? 90 : 70;

        $highFreq = 0; $total = 0;
        for ($x = 0; $x < $w - 4; $x += 4) {
            for ($y = 0; $y < $h - 4; $y += 4) {
                if (abs((imagecolorat($img, $x, $y) & 0xFF) - (imagecolorat($img, $x + 4, $y) & 0xFF)) > 25) $highFreq++;
                $total++;
            }
        }
        $textureRatio = $total > 0 ? $highFreq / $total : 0;
        $scores['texture'] = $textureRatio > 0.35 ? 50 : ($textureRatio > 0.2 ? 75 : 90);
        imagedestroy($img);

        $totalScore = $scores['brightness'] * 0.4 + $scores['texture'] * 0.6;
        $passed = $totalScore >= 75;
        return ['success' => $passed, 'liveness_score' => round($totalScore, 1), 'message' => $passed ? '活体检测通过' : '活体检测未通过，请确保是真人操作', 'details' => $scores];
    }
}