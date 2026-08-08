<?php
namespace app\controller\api;

use app\controller\Base;
use extend\IdCardValidator;

class Idcard extends Base
{
    public function verify()
    {
        $name = request()->post('name', '');
        $idCard = request()->post('id_card', '');
        if (empty($name) || mb_strlen($name) < 2) return $this->error('请输入有效姓名');
        if (empty($idCard)) return $this->error('请输入身份证号');

        $validator = new IdCardValidator();
        $result = $validator->verify($idCard);
        if (!$result['valid']) return $this->error($result['message']);

        return $this->success([
            'name' => $name, 'gender' => $result['gender'],
            'gender_text' => $result['gender_text'], 'birth_date' => $result['birth_date'],
            'age' => $result['age'],
        ], '身份证号校验通过');
    }
}