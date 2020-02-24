<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */

namespace Yiisoft\Files\Adapter;

use Yiisoft\Files\Dto\LocalAdapterDTO;
use Yiisoft\Files\Helper\AdapterHelper;

/**
 * This class of adapter for working with Local
 * To work with this adapter, pass @see \Yiisoft\Files\Dto\LocalAdapterDTO
 * This class extends the flysystem class, for more information about the adapter
 * @see https://flysystem.thephpleague.com/v1/docs/adapter/local/
 * @see https://github.com/thephpleague/flysystem/blob/1.x/src/Adapter/Local.php
 * @see https://github.com/thephpleague/flysystem
 *
 * Class LocalAdapter
 * @package Yiisoft\Files\Adapter
 */
class LocalAdapter extends \League\Flysystem\Adapter\Local
{
    /**
     * @var LocalAdapterDTO
     */
    public $connectionParams;

    /**
     * Local constructor.
     * @param $rootOrDto
     * @param mixed ...$args
     */
    function __construct($rootOrDto, ...$args)
    {
        if (is_a($rootOrDto, LocalAdapterDTO::class)) {

            AdapterHelper::validation([
                'writeFlags',
                'linkHandling',
                'permissions'
            ], $rootOrDto);
            $this->connectionParams = $rootOrDto;
            return parent::__construct(
                $rootOrDto->root,
                $rootOrDto->writeFlags,
                $rootOrDto->linkHandling,
                $rootOrDto->permissions
            );
        }

        return parent::__construct(
            $rootOrDto,
            ...$args
        );
    }
}
