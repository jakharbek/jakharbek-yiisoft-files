<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */

namespace Yiisoft\Files;

use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use Yiisoft\Files\Adapter\AdapterFactory;
use Yiisoft\Files\Adapter\LocalAdapter;
use Yiisoft\Files\Dto\LocalAdapterDTO;
use Yiisoft\Files\Exception\FileException;
use Yiisoft\Files\Helper\ConfigHelper;
use Yiisoft\Files\Traits\StorageTrait;

/**
 * Class Storage
 * @package Yiisoft\Files
 */
class Storage
{
    use StorageTrait;

    /**
     * @var string
     */
    protected $root;

    /**
     * @var object|string|array
     */
    protected $connectionParams;

    /**
     * @var array
     */
    protected $configParams;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var Filesystem
     */
    private $_filesystem;

    /**
     * @var AdapterInterface
     */
    private $_adapter;

    /**
     * @var File
     */
    private $_file;

    /**
     * Storage constructor.
     * @param $connectionParams
     * @param null $configParams
     * @throws Exception\AdapterException
     */
    function __construct($connectionParams, $configParams = null)
    {
        $this->setConnectionParams($connectionParams);
        $this->setConfigParams($configParams);
        $this->connection();
    }

    /**
     * @throws Exception\AdapterException
     */
    public function connection()
    {
        $this->_filesystem = new Filesystem($this->getAdapter(), $this->getConfigParams());
        return $this;
    }

    /**
     * @return AdapterInterface|Adapter\FtpAdapter|LocalAdapter|Adapter\NullAdapter
     * @throws \Exception
     */
    public function getAdapter()
    {
        if (!is_object($this->_adapter)) {
            $this->_adapter = AdapterFactory::create($this->getConnectionParams());
        }

        return $this->_adapter;
    }

    /**
     * @return mixed
     */
    public function getConnectionParams()
    {
        return (strlen($this->connectionParams) == 0) ? null : unserialize($this->connectionParams);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function setConnectionParams($params)
    {
        if (is_object($params) || (is_array($params) && count($params) > 0)) {
            $this->connectionParams = serialize($params);
        } else {
            $this->connectionParams = (strlen($params) == 0) ? null : serialize($params);
        }

        (is_object($params)) ? $this->type = get_class($params) : (is_array($params) ? $this->type = "array" : "string");
        return $this->getConnectionParams();
    }

    /**
     * @return mixed
     */
    public function getConfigParams()
    {
        return (strlen($this->configParams) == 0) ? null : unserialize($this->configParams);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function setConfigParams($params)
    {
        $this->configParams = (strlen($params) == 0) ? null : serialize($params);
        return $this->getConfigParams();
    }

    /**
     * @return Storage
     * @throws Exception\AdapterException
     */
    public static function getLocalStorage($file = null)
    {
        $dto = new LocalAdapterDTO();
        $dto->root = ConfigHelper::getParam('file.storage')['local']['root'];
        $dto->permissions = ConfigHelper::getParam('file.storage')['local']['permissions'];
        $dto->linkHandling = ConfigHelper::getParam('file.storage')['local']['linkHandling'] ?? Local::DISALLOW_LINKS;
        $dto->writeFlags = ConfigHelper::getParam('file.storage')['local']['writeFlags'] ?? LOCK_EX;
        $storage = new Storage($dto);
        $storage->setAlias("local");
        if ($file !== null) {
            $storage->setFile($file);
        }
        return $storage;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setAlias($value)
    {
        return $this->alias = $value;
    }


    /**
     * @throws FileException
     */
    public function isFile()
    {
        if (!is_a($this->getFile(), File::class)) {
            throw new FileException("File is not exists");
        }
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * @param File $file
     * @return File
     */
    public function setFile(File $file)
    {
        $this->_file = $file;
        return $this->getFile();
    }


    /**
     * @return Filesystem
     * @throws Exception\AdapterException
     */
    public function getFilesystem()
    {
        if (!is_object($this->_filesystem)) {
            $this->connection();
        }

        return $this->_filesystem;
    }

    /**
     * @param $source
     * @return bool
     * @throws Exception\AdapterException
     */
    public function has($source = null)
    {
        return $this->getFilesystem()->has($source);
    }


    /**
     * @return mixed
     * @throws Exception\AdapterException
     */
    public function getRoot()
    {
        if (is_a($this->getAdapter(), LocalAdapter::class)) {
            return ConfigHelper::getParam('file.storage')['local']['root'];
        }
        return $this->root;
    }

    /**
     * @param $value
     * @return mixed
     * @throws Exception\AdapterException
     */
    public function setRoot($value)
    {
        $this->root = $value;
        return $this->getRoot();
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this->getTemplate();
    }

}
