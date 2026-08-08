<?php
namespace extend;

class IdCardValidator
{
    const WEIGHT = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
    const CHECK  = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

    public function verify($idCard)
    {
        if (!preg_match('/^\d{17}[\dXx]$/', $idCard)) return ['valid' => false, 'message' => '身份证号格式不正确'];
        $idCard = strtoupper($idCard);
        $year = (int)substr($idCard, 6, 4); $month = (int)substr($idCard, 10, 2); $day = (int)substr($idCard, 12, 2);
        if (!checkdate($month, $day, $year) || $year < 1900 || $year > date('Y')) return ['valid' => false, 'message' => '出生日期无效'];
        $sum = 0; for ($i = 0; $i < 17; $i++) $sum += (int)$idCard[$i] * self::WEIGHT[$i];
        if ($idCard[17] !== self::CHECK[$sum % 11]) return ['valid' => false, 'message' => '校验码错误'];
        $gender = ((int)$idCard[16] % 2 === 1) ? 'male' : 'female';
        $birth = new \DateTime("{$year}-{$month}-{$day}"); $age = $birth->diff(new \DateTime())->y;
        return ['valid' => true, 'message' => '校验通过', 'gender' => $gender, 'gender_text' => $gender === 'male' ? '男' : '女', 'birth_date' => sprintf('%04d-%02d-%02d', $year, $month, $day), 'age' => $age];
    }
}