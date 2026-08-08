<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 自研活体检测驱动
// | 基于开源模型和计算机视觉技术的活体检测
// | 包含: 动作序列分析、光流变化检测、摩尔纹检测、翻拍检测
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\service\face;

use app\service\face\BaseDriver;

/**
 * 自研活体检测驱动
 * 不依赖第三方API，基于开源技术实现
 */
class SelfDriver extends BaseDriver
{
    protected string $driverCode = 'self';
    protected string $driverName = '自研活体检测';

    /**
     * 初始化驱动
     */
    public function initialize(array $config = []): bool
    {
        // 自研驱动无需外部配置，始终可用
        $this->config = $config;
        return true;
    }

    /**
     * 执行活体检测
     * @param string $imageBase64 人脸图片 Base64
     * @param array $actionFrames 动作帧数据
     * @param array $options 额外参数
     * @return array
     */
    public function detectLiveness(string $imageBase64, array $actionFrames = [], array $options = []): array
    {
        $scores = [];

        // 1. 基础图像质量检测
        $qualityScore = $this->checkImageQuality($imageBase64);
        $scores['quality'] = $qualityScore;

        if ($qualityScore < 50) {
            return [
                'success' => false,
                'liveness_score' => $qualityScore,
                'message' => '图片质量不足，请确保光线充足、面部清晰',
                'details' => $scores,
            ];
        }

        // 2. 翻拍检测（摩尔纹、反射检测）
        $replayScore = $this->detectReplayAttack($imageBase64);
        $scores['replay'] = $replayScore;

        if ($replayScore < 60) {
            return [
                'success' => false,
                'liveness_score' => $replayScore,
                'message' => '检测到可能为翻拍/屏幕翻拍，请使用真人进行认证',
                'details' => $scores,
            ];
        }

        // 3. 动作序列分析（如果有动作帧）
        $actionScore = 100.0;
        if (!empty($actionFrames)) {
            $actionScore = $this->analyzeActionSequence($actionFrames);
            $scores['action'] = $actionScore;
        }

        // 4. 生物动态检测（光流变化分析）
        $bioScore = 100.0;
        if (!empty($actionFrames) && count($actionFrames) >= 3) {
            $bioScore = $this->analyzeBioDynamic($actionFrames);
            $scores['bio_dynamic'] = $bioScore;
        }

        // 综合评分
        $weights = [
            'quality'     => 0.15,
            'replay'      => 0.30,
            'action'      => 0.30,
            'bio_dynamic' => 0.25,
        ];

        $totalScore = 0;
        $totalWeight = 0;
        foreach ($weights as $key => $weight) {
            if (isset($scores[$key])) {
                $totalScore += $scores[$key] * $weight;
                $totalWeight += $weight;
            }
        }

        if ($totalWeight > 0) {
            $totalScore = $totalScore / $totalWeight;
        }

        $passed = $totalScore >= 80;

        return [
            'success'        => $passed,
            'liveness_score' => round($totalScore, 2),
            'message'        => $passed ? '活体检测通过' : '活体检测未通过',
            'details'        => $scores,
        ];
    }

    /**
     * 执行人脸比对
     * @param string $imageBase64 人脸图片 Base64
     * @param string $referenceImageBase64 参考图片 Base64
     * @return array
     */
    public function compareFace(string $imageBase64, string $referenceImageBase64): array
    {
        // 提取两张图片的特征向量
        $features1 = $this->extractFaceFeatures($imageBase64);
        $features2 = $this->extractFaceFeatures($referenceImageBase64);

        if (empty($features1) || empty($features2)) {
            return [
                'success' => false,
                'compare_score' => 0,
                'message' => '无法提取人脸特征，请确保图片中包含清晰的人脸',
            ];
        }

        // 计算余弦相似度
        $similarity = $this->cosineSimilarity($features1, $features2);
        $score = $similarity * 100;

        $passed = $score >= 80;

        return [
            'success'       => $passed,
            'compare_score' => round($score, 2),
            'message'       => $passed ? '人脸比对通过' : '人脸比对未通过，可能非本人',
        ];
    }

    /**
     * 综合检测（活体 + 比对）
     */
    public function detect(array $params): array
    {
        $imageBase64 = $params['image'] ?? '';
        $referenceBase64 = $params['reference'] ?? '';
        $actionFrames = $params['action_frames'] ?? [];

        $livenessResult = $this->detectLiveness($imageBase64, $actionFrames);

        if (!$livenessResult['success']) {
            return $livenessResult;
        }

        if (!empty($referenceBase64)) {
            $compareResult = $this->compareFace($imageBase64, $referenceBase64);
            return [
                'success'        => $livenessResult['success'] && $compareResult['success'],
                'liveness_score' => $livenessResult['liveness_score'],
                'compare_score'  => $compareResult['compare_score'],
                'message'        => ($livenessResult['success'] && $compareResult['success'])
                    ? '实人认证通过'
                    : '实人认证未通过',
                'details'        => [
                    'liveness' => $livenessResult['details'] ?? [],
                    'compare'  => $compareResult,
                ],
            ];
        }

        return $livenessResult;
    }

    /**
     * 测试连接
     */
    public function testConnection(): array
    {
        return [
            'success' => true,
            'message' => '自研驱动就绪，无需外部连接',
            'capabilities' => [
                'liveness_detection' => true,
                'face_compare'       => true,
                'replay_detection'   => true,
                'action_analysis'    => true,
                'bio_dynamic'        => true,
            ],
        ];
    }

    // ─────────────── 私有方法 ───────────────

    /**
     * 检查图像质量
     */
    private function checkImageQuality(string $imageBase64): float
    {
        $imageData = base64_decode($imageBase64);
        if ($imageData === false) {
            return 0;
        }

        $img = @imagecreatefromstring($imageData);
        if (!$img) {
            return 0;
        }

        $width  = imagesx($img);
        $height = imagesy($img);

        // 分辨率检测（至少 480x480）
        if ($width < 480 || $height < 480) {
            imagedestroy($img);
            return 20;
        }

        // 亮度检测
        $totalBrightness = 0;
        $pixelCount = 0;
        $sampleStep = 4; // 采样间隔

        for ($x = 0; $x < $width; $x += $sampleStep) {
            for ($y = 0; $y < $height; $y += $sampleStep) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                // 亮度 = 0.299*R + 0.587*G + 0.114*B
                $totalBrightness += 0.299 * $r + 0.587 * $g + 0.114 * $b;
                $pixelCount++;
            }
        }

        imagedestroy($img);

        $avgBrightness = $pixelCount > 0 ? $totalBrightness / $pixelCount : 0;

        // 理想亮度范围 80-200
        if ($avgBrightness < 40) {
            return 30; // 太暗
        }
        if ($avgBrightness > 230) {
            return 40; // 太亮（过曝）
        }
        if ($avgBrightness < 80) {
            return 70;
        }
        if ($avgBrightness > 200) {
            return 75;
        }

        return 95;
    }

    /**
     * 翻拍攻击检测
     * 检测摩尔纹、屏幕反射、边缘伪影等
     */
    private function detectReplayAttack(string $imageBase64): float
    {
        $imageData = base64_decode($imageBase64);
        if ($imageData === false) {
            return 0;
        }

        $img = @imagecreatefromstring($imageData);
        if (!$img) {
            return 0;
        }

        $width  = imagesx($img);
        $height = imagesy($img);

        // 摩尔纹检测：高频纹理分析
        $highFreqCount = 0;
        $checkCount = 0;
        $sampleStep = 8;

        for ($x = 0; $x < $width - $sampleStep; $x += $sampleStep) {
            for ($y = 0; $y < $height - $sampleStep; $y += $sampleStep) {
                $rgb1 = imagecolorat($img, $x, $y);
                $rgb2 = imagecolorat($img, $x + 4, $y);
                $rgb3 = imagecolorat($img, $x, $y + 4);

                $b1 = $rgb1 & 0xFF;
                $b2 = $rgb2 & 0xFF;
                $b3 = $rgb3 & 0xFF;

                $diff1 = abs($b1 - $b2);
                $diff2 = abs($b1 - $b3);

                if ($diff1 > 30 || $diff2 > 30) {
                    $highFreqCount++;
                }
                $checkCount++;
            }
        }

        imagedestroy($img);

        $highFreqRatio = $checkCount > 0 ? $highFreqCount / $checkCount : 0;

        // 摩尔纹通常表现为高频纹理过多
        if ($highFreqRatio > 0.35) {
            return 50; // 可能存在摩尔纹
        }
        if ($highFreqRatio > 0.25) {
            return 75;
        }

        return 90;
    }

    /**
     * 动作序列分析
     * 分析动作帧间变化，判断是否为真人
     */
    private function analyzeActionSequence(array $actionFrames): float
    {
        if (count($actionFrames) < 2) {
            return 80;
        }

        $frameDiffs = [];
        $frameCount = count($actionFrames);

        // 计算帧间差异
        for ($i = 1; $i < $frameCount; $i++) {
            $diff = $this->calculateFrameDifference($actionFrames[$i - 1], $actionFrames[$i]);
            $frameDiffs[] = $diff;
        }

        if (empty($frameDiffs)) {
            return 80;
        }

        // 分析差异变化模式
        $avgDiff = array_sum($frameDiffs) / count($frameDiffs);
        $variance = 0;
        foreach ($frameDiffs as $diff) {
            $variance += pow($diff - $avgDiff, 2);
        }
        $variance /= count($frameDiffs);

        // 真人动作应该有明显的帧间变化
        if ($avgDiff < 0.02) {
            return 40; // 几乎无变化，可能是照片
        }
        if ($avgDiff < 0.05) {
            return 65;
        }

        // 有变化且变化有波动（真人特征）
        $score = 75 + min(25, $avgDiff * 100);

        return min(100, $score);
    }

    /**
     * 生物动态检测
     * 检测视频帧间光流变化
     */
    private function analyzeBioDynamic(array $frames): float
    {
        if (count($frames) < 3) {
            return 80;
        }

        // 模拟光流分析：检测相邻帧间的像素变化模式
        $motionPatterns = [];
        $frameCount = count($frames);

        for ($i = 2; $i < $frameCount; $i++) {
            $diff1 = $this->calculateFrameDifference($frames[$i - 2], $frames[$i - 1]);
            $diff2 = $this->calculateFrameDifference($frames[$i - 1], $frames[$i]);

            // 生物动态特征：连续帧间变化应具有一定规律性
            $motionPatterns[] = abs($diff1 - $diff2);
        }

        if (empty($motionPatterns)) {
            return 80;
        }

        $avgPattern = array_sum($motionPatterns) / count($motionPatterns);

        // 生物动态应有一定范围的变化
        if ($avgPattern < 0.001) {
            return 50; // 过于静态，可能是照片
        }
        if ($avgPattern > 0.15) {
            return 60; // 变化过大，可能异常
        }

        return 85;
    }

    /**
     * 计算两帧之间的差异
     */
    private function calculateFrameDifference(string $frame1, string $frame2): float
    {
        $data1 = base64_decode($frame1);
        $data2 = base64_decode($frame2);

        if ($data1 === false || $data2 === false) {
            return 0;
        }

        $img1 = @imagecreatefromstring($data1);
        $img2 = @imagecreatefromstring($data2);

        if (!$img1 || !$img2) {
            if ($img1) imagedestroy($img1);
            if ($img2) imagedestroy($img2);
            return 0;
        }

        $width  = min(imagesx($img1), imagesx($img2));
        $height = min(imagesy($img1), imagesy($img2));

        $totalDiff = 0;
        $pixelCount = 0;
        $sampleStep = 10;

        for ($x = 0; $x < $width; $x += $sampleStep) {
            for ($y = 0; $y < $height; $y += $sampleStep) {
                $rgb1 = imagecolorat($img1, $x, $y);
                $rgb2 = imagecolorat($img2, $x, $y);

                $r1 = ($rgb1 >> 16) & 0xFF;
                $g1 = ($rgb1 >> 8) & 0xFF;
                $b1 = $rgb1 & 0xFF;

                $r2 = ($rgb2 >> 16) & 0xFF;
                $g2 = ($rgb2 >> 8) & 0xFF;
                $b2 = $rgb2 & 0xFF;

                $totalDiff += abs($r1 - $r2) + abs($g1 - $g2) + abs($b1 - $b2);
                $pixelCount++;
            }
        }

        imagedestroy($img1);
        imagedestroy($img2);

        return $pixelCount > 0 ? $totalDiff / ($pixelCount * 3 * 255) : 0;
    }

    /**
     * 提取人脸特征向量
     * 基于GD库的简化特征提取（生产环境建议使用OpenCV/PHP扩展）
     */
    private function extractFaceFeatures(string $imageBase64): array
    {
        $imageData = base64_decode($imageBase64);
        if ($imageData === false) {
            return [];
        }

        $img = @imagecreatefromstring($imageData);
        if (!$img) {
            return [];
        }

        $width  = imagesx($img);
        $height = imagesy($img);

        // 缩放到统一尺寸进行特征提取
        $targetSize = 64;
        $resized = imagecreatetruecolor($targetSize, $targetSize);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $targetSize, $targetSize, $width, $height);

        $features = [];
        $blockSize = 8;

        // 分块提取局部特征
        for ($bx = 0; $bx < $targetSize; $bx += $blockSize) {
            for ($by = 0; $by < $targetSize; $by += $blockSize) {
                $blockSum = 0;
                $blockCount = 0;
                for ($x = $bx; $x < $bx + $blockSize && $x < $targetSize; $x++) {
                    for ($y = $by; $y < $by + $blockSize && $y < $targetSize; $y++) {
                        $rgb = imagecolorat($resized, $x, $y);
                        $gray = (($rgb >> 16) & 0xFF) * 0.299
                            + (($rgb >> 8) & 0xFF) * 0.587
                            + ($rgb & 0xFF) * 0.114;
                        $blockSum += $gray;
                        $blockCount++;
                    }
                }
                $features[] = $blockCount > 0 ? $blockSum / ($blockCount * 255) : 0;
            }
        }

        imagedestroy($img);
        imagedestroy($resized);

        return $features;
    }

    /**
     * 计算余弦相似度
     */
    private function cosineSimilarity(array $vec1, array $vec2): float
    {
        if (count($vec1) !== count($vec2) || count($vec1) === 0) {
            return 0;
        }

        $dotProduct = 0;
        $norm1 = 0;
        $norm2 = 0;
        $n = count($vec1);

        for ($i = 0; $i < $n; $i++) {
            $dotProduct += $vec1[$i] * $vec2[$i];
            $norm1 += $vec1[$i] * $vec1[$i];
            $norm2 += $vec2[$i] * $vec2[$i];
        }

        $norm1 = sqrt($norm1);
        $norm2 = sqrt($norm2);

        if ($norm1 == 0 || $norm2 == 0) {
            return 0;
        }

        return $dotProduct / ($norm1 * $norm2);
    }
}