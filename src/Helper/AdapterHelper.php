<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */


namespace Yiisoft\Files\Helper;


use Yiisoft\Files\Exception\AdapterException;

/**
 * Class AdapterHelper
 * @package Yiisoft\Files\Helper
 */
class AdapterHelper
{
    /**
     * @param array $attributes
     * @return array
     */
    public static function clear(array &$attributes)
    {
        if (count($attributes) == 0) {
            return $attributes;
        }

        foreach ($attributes as $key => &$attribute) {
            $attribute = trim($attribute);
            if ($attribute == null || strlen($attribute) == 0) {
                unset($attributes[$key]);
            }
        }

        return $attributes;
    }

    /**
     * @param $attr
     * @param $attributes
     * @param string $exceptionClass
     */
    public static function validation(array $requiredAttributes, $attributes, $exceptionClass = AdapterException::class)
    {
        $attributes = (array)$attributes;
        foreach ($requiredAttributes as $key => $requiredAttribute) {
            if (is_array($requiredAttribute)) {
                self::validate($key, $requiredAttributes, $exceptionClass);
                self::validation($requiredAttribute, $attributes[$key], $exceptionClass);
            }
            self::validate($requiredAttribute, $attributes, $exceptionClass);
        }
    }

    /**
     * @param $attr
     * @param $attributes
     * @param string $exceptionClass
     */
    public static function validate($attr, $attributes, $exceptionClass = AdapterException::class)
    {
        $attributes = (array)$attributes;

        if (!array_key_exists($attr, $attributes)) {
            throw new $exceptionClass("The \"{$attr}\" property must be set.");
        }

        if (is_array($attributes[$attr]) && count($attributes[$attr]) == 0) {
            throw new $exceptionClass("The \"{$attr}\" property must be set.");
        }

        if (is_string($attributes[$attr]) && strlen(trim($attributes[$attr])) == 0) {
            throw new $exceptionClass("The \"{$attr}\" property must be set.");
        }
    }
}
