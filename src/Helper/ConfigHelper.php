<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */


namespace Yiisoft\Files\Helper;

/**
 * Class ConfigHelper
 * @package Yiisoft\Files\Helper
 */
class ConfigHelper
{
    /**
     * @param $param
     * @return mixed
     */
    public static function getParam($param)
    {
        $params = self::getParams();
        return $params[$param] ?? null;
    }

    public static function getParams()
    {
        $params = require \hiqdev\composer\config\Builder::path('params');
        return $params;
    }
}
