<?php
// +----------------------------------------------------------------------
// | 森码云实人认证系统 - 模型基类
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * 模型基类
 */
class BaseModel extends Model
{
    /**
     * 自动写入时间戳
     */
    protected $autoWriteTimestamp = true;

    /**
     * 时间字段格式
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * 创建时间字段
     */
    protected $createTime = 'create_time';

    /**
     * 更新时间字段
     */
    protected $updateTime = 'update_time';

    /**
     * 获取分页数据
     */
    public static function getPageList(int $page = 1, int $limit = 20, array $where = [], string $order = 'id desc'): array
    {
        $query = static::where($where);
        $total = $query->count();
        $list  = $query->order($order)->page($page, $limit)->select()->toArray();

        return [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'list'  => $list,
        ];
    }
}