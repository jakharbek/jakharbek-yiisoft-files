<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */


namespace Yiisoft\Files\Adapter;

use Yiisoft\Files\Dto\FtpAdapterDTO;
use Yiisoft\Files\Exception\AdapterException;
use Yiisoft\Files\Helper\AdapterHelper;

/**
 * This class of adapter for working with FTP
 * To work with this adapter, pass @see \Yiisoft\Files\Dto\FtpAdapterDTO
 * This class extends the flysystem class, for more information about the adapter
 * @see https://flysystem.thephpleague.com/v1/docs/adapter/ftp/
 * @see https://github.com/thephpleague/flysystem/blob/1.x/src/Adapter/Ftp.php
 * @see https://github.com/thephpleague/flysystem
 *
 * Class FtpAdapter
 * @package Yiisoft\Files\Adapter
 */
class FtpAdapter extends \League\Flysystem\Adapter\Ftp
{
    public $connectionParams;

    /**
     * Ftp constructor.
     * @param mixed ...$args
     * @throws \Exception
     */
    function __construct(...$args)
    {
        if (func_num_args() == 0) {
            throw new AdapterException("Please enter either FtpAdapterDTO or pass argument according to FtpAdapterDTO.");
        }

        if (func_num_args() == 1 && is_a($args[0], FtpAdapterDTO::class)) {
            $dto = $args[0];
            $this->connectionParams = $dto;
            $this->validation($dto);
            $config = (array)$dto;
            AdapterHelper::clear($config);
            return parent::__construct($config);
        }

        $this->validation($args);
        $config = $args;
        AdapterHelper::clear($args);
        return parent::__construct(...$config);
    }

    /**
     * @param $configuration
     */
    private function validation($configuration)
    {
        AdapterHelper::validation(['host', 'username', 'password'], $configuration);
    }
}
