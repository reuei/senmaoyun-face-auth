<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 认证记录模型
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\model;

class CertifyRecord extends BaseModel
{
    protected $name = 'certify_record';

    /**
     * 状态枚举
     */
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESS    = 'success';
    const STATUS_FAILED     = 'failed';
    const STATUS_AUDITING   = 'auditing';

    /**
     * 生成记录编号
     */
    public static function generateRecordNo(): string
    {
        return date('YmdHis') . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
    }

    /**
     * 格式化姓名（脱敏）
     */
    public function getMaskedName(): string
    {
        $name = $this->getData('name');
        $name = app('encryption')->decrypt($name);
        if (mb_strlen($name) <= 2) {
            return mb_substr($name, 0, 1) . '*';
        }
        return mb_substr($name, 0, 1) . str_repeat('*', mb_strlen($name) - 2) . mb_substr($name, -1);
    }

    /**
     * 格式化身份证号（脱敏）
     */
    public function getMaskedIdCard(): string
    {
        $idCard = app('encryption')->decrypt($this->getData('id_card'));
        return substr($idCard, 0, 4) . '**********' . substr($idCard, -4);
    }
}