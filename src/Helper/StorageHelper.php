<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */


namespace Yiisoft\Files\Helper;

/**
 * Class StorageHelper
 * @package Yiisoft\Files\Helper
 */
class StorageHelper
{
    /**
     * @param $dist
     * @param $template
     * @return string
     */
    public static function getPathFromTemplate($dist, $template)
    {
        $args = [
            ':year' => date("Y"),
            ':month' => date("m"),
            ':day' => date("d"),
            ':hour' => date("H"),
            ':minute' => date("i"),
            ':second' => date("s")
        ];

        foreach ($args as $key => $value) {
            $template = str_replace($key, $value, $template);
        }

        return $template.$dist;
    }
}
