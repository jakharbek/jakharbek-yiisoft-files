<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */

namespace Yiisoft\Files\Adapter;


use Yiisoft\Files\Dto\FtpAdapterDTO;
use Yiisoft\Files\Dto\LocalAdapterDTO;
use Yiisoft\Files\Dto\NullAdapterDTO;

/**
 * This class is a factory class for adapters in this class. You can create any adapter you need.
 * List of available adapters:
 * FTP              - @see  \Yiisoft\Files\Dto\FtpAdapterDTO
 * Local            - @see  \Yiisoft\Files\Dto\LocalAdapterDTO
 * Null             - @see  \Yiisoft\Files\Dto\NullAdapterDTO
 * Class AdapterFactory
 * @package Yiisoft\Files\Adapter
 */
class AdapterFactory
{
    /**
     * @param $dto
     * @return FtpAdapter|LocalAdapter|NullAdapter
     * @throws \Exception
     */
    public static function create($dto)
    {
        switch ($dto) {
            case is_a($dto, FtpAdapterDTO::class) :
                return new FtpAdapter($dto);
            case is_a($dto, LocalAdapterDTO::class) :
                return new LocalAdapter($dto);
            case is_a($dto, NullAdapterDTO::class) :
                return new NullAdapter($dto);
            default:
                $className = get_class($dto);
                throw new AdapterException("Adapter DTO is not founded:" . $className);
        }
    }
}
