<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 身份证校验API
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\controller\api;

use app\controller\Base;
use app\service\IdCardService;

class Idcard extends Base
{
    /**
     * 校验身份证号
     * POST /api/idcard/verify
     */
    public function verify()
    {
        $name   = request()->post('name', '');
        $idCard = request()->post('id_card', '');

        if (empty($name)) {
            return $this->error('请输入姓名');
        }
        if (empty($idCard)) {
            return $this->error('请输入身份证号');
        }

        // 姓名格式校验
        if (mb_strlen($name) < 2) {
            return $this->error('姓名长度至少2个字符');
        }

        $service = new IdCardService();
        $result  = $service->verify($idCard);

        if (!$result['valid']) {
            return $this->error($result['message']);
        }

        return $this->success([
            'name'       => $name,
            'id_card'    => substr($idCard, 0, 4) . '**********' . substr($idCard, -4),
            'gender'     => $result['gender'],
            'gender_text'=> $result['gender_text'],
            'birth_date' => $result['birth_date'],
            'age'        => $result['age'],
            'area_name'  => $result['area_name'],
        ], '身份证号校验通过');
    }
}