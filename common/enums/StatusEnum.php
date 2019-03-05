<?php
namespace common\enums;

/**
 * Class StatusEnum
 * @package common\enums
 * @author cx
 */
class StatusEnum
{
    const ENABLED = 1;
    const DISABLED = 0;
    const DELETE = -1;

    /**
     * @var array
     */
    public static $listExplain = [
        self::ENABLED => '启用',
        self::DISABLED => '禁用',
    ];
}
