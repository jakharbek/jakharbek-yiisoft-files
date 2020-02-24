<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */

namespace Yiisoft\Files\Dto;


use Yiisoft\Files\Adapter\FtpAdapter;

/**
 * This class of DTO for working with FTP
 * This DTO for work with @see FtpAdapter
 * This DTO implements the flysystem arguments, for more information
 *
 * Class FtpAdapterDTO
 * @package Dto
 */
class FtpAdapterDTO
{
    public $host;
    public $username;
    public $password;
    public $port = 21;
    public $ssl = false;
    public $timeout = 90;
    public $root = "/";
    public $permPublic = 0744;
    public $permPrivate = 0700;
    public $passive = true;
    public $transferMode = FTP_BINARY;
    public $systemType;
    public $ignorePassiveAddress;
    public $recurseManually = false;
    public $utf8 = false;
    public $enableTimestampsOnUnixListings = false;
}
