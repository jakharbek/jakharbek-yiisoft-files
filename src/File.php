<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */

namespace Yiisoft\Files;

use Yiisoft\Files\Helper\FileHelper;
use Yiisoft\Files\Traits\FileTrait;

/**
 * Class File
 * @package Yiisoft\Filess
 */
class File
{
    use FileTrait;

    private $storage;
    private $source;

    /**
     * File constructor.
     * @param $name
     * @throws FileException
     */
    public function __construct($name)
    {
        $this->initSource($name);
        $this->setStorage(Storage::getLocalStorage($this));
    }

    /**
     * @param $name
     * @throws FileException
     */
    private function initSource($name)
    {
        if (preg_match("/[$]/m", $name)) {
            $name = str_replace("$", null, $name);
            $name = FileHelper::getPathFromFiles($name);
        }

        $this->setPath($name);
    }

    /**
     * @param null $path
     * @return string|null
     * @throws \Exception
     */
    private function setPath($path = null): ?string
    {
        $this->source = $path;
        return $this->getSource();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return basename($this->getSource());
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->getSource());
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        return $this->getInfo()['basename'];
    }

    /**
     * @return string|string[]
     */
    public function getInfo()
    {
        return pathinfo($this->getSource());
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->getInfo()['extension'];
    }

    /**
     * @return string|string[]
     * @throws \Exception
     */
    public function relativePath()
    {
        return str_replace($this->getStorage()->getRoot(), null, $this->getSource());
    }

    /**
     * @return Storage
     * @throws \Exception
     */
    public function getStorage(): Storage
    {
        return $this->storage;
    }

    /**
     * @param $value
     * @return Storage
     * @throws \Exception
     */
    public function setStorage($value): Storage
    {
        $this->storage = $value;
        return $this->getStorage();
    }

    /**
     * @return false|resource
     */
    public function getStream()
    {
        return fopen($this->getSource(), 'r+');
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        return mime_content_type($this->getSource());
    }

    /**
     * @return false|int
     */
    public function getTimestamp()
    {
        return filemtime($this->getSource());
    }

    /**
     * @return false|int
     */
    public function getSize()
    {
        return filesize($this->getSource());
    }

    /**
     * @return string|string[]
     * @throws \Exception
     */
    public function relativeDir()
    {
        return str_replace($this->getStorage()->getRoot(), $this->getDir());
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->getInfo()['dirname'];
    }

    /**
     * @return false|string
     */
    public function getContents()
    {
        return file_get_contents($this->getSource());
    }


}
