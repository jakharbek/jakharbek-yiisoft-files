<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */

namespace Yiisoft\Files;

use League\Flysystem\FileNotFoundException;
use Yiisoft\Files\Exception\FileException;
use Yiisoft\Files\Helper\FileHelper;
use Yiisoft\Files\Helper\MimeTypeHelper;
use Yiisoft\Files\Traits\FileTrait;

/**
 * This class implements file abstraction in the file system.
 * There are three ways to access these files any of these ways will help you access any or most file system:
 * Local - If your file is located locally or outside the local storage that you defined                    -   @see File::local()
 * From - If your file is located in the storage, then you can use this method to access the file           -   @see File::from()
 * Stream - For streaming files, you can use this method                                                    -   @see File::stream()
 * Form - For convenience, you can use this method if you submit a file through the form                    -   @see File::from()
 *
 * Class File
 * @package Yiisoft\Filess
 */
class File
{
    use FileTrait;

    private $storage = null;
    private $_path = null;
    private $_stream = null;
    private $_info = null;
    private $_local = null;


    /**
     * @param $path
     * @param Storage|null $storage
     * @return File
     * @throws Exception\AdapterException
     */
    public static function from($path, Storage $storage = null)
    {
        $file = new File();
        $file->setPath($path);
        $file->initStorage($storage);
        return $file;
    }


    /**
     * @param null $path
     * @return string|null
     */
    private function setPath($path = null): ?string
    {
        $this->_path = $path;
        return $this->getPath();
    }

    /**
     * @return null
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @param Storage|null $storage
     * @return Storage
     * @throws Exception\AdapterException
     */
    public function initStorage(Storage $storage = null): ?Storage
    {
        if (!($storage instanceof Storage)) {
            $storage = Storage::getLocalStorage($this);
        }

        $storage->setFile($this);
        $this->setStorage($storage);
        return $this->getStorage();
    }

    /**
     * @return Storage
     * @throws \Exception
     */
    public function getStorage(): ?Storage
    {
        return $this->storage;
    }

    /**
     * @param $value
     * @return Storage|null
     * @throws \Exception
     */
    public function setStorage($value): ?Storage
    {
        $this->storage = $value;
        return $this->getStorage();
    }

    /**
     * @param $name
     * @return File
     * @throws FileException
     */
    public static function form($name)
    {
        return self::local(FileHelper::getPathFromFiles($name));
    }

    /**
     * @param $dist
     * @return File|null
     * @throws FileException
     */
    public static function local($dist): ?File
    {
        if (!file_exists($dist)) {
            throw new FileException("File is not founded");
        }

        $file = new File();
        $file->setLocal($dist);
        $file->initStream(fopen($file->getLocal(), "r+"));
        return $file;
    }

    /**
     * @param $stream
     * @return false|resource
     */
    public function initStream($stream)
    {
        return $this->setStream($stream);
    }

    /**
     * @return null
     */
    public function getLocal()
    {
        return $this->_local;
    }


    /**
     * @param $local
     * @return string|null
     */
    public function setLocal($local): ?string
    {
        $this->_local = $local;
        return $this->getLocal();
    }

    /**
     * @param $stream
     * @return File
     * @throws FileException
     */
    public static function stream($stream): ?File
    {
        if (!is_resource($stream)) {
            throw new FileException("Stream is not founded");
        }

        $file = new File();
        $file->initStream($stream);
        return $file;
    }


    /**
     * @return bool
     * @throws Exception\AdapterException
     * @throws FileException
     */
    public function exists()
    {
        if ($this->hasLocal()) {
            return file_exists($this->getLocal());
        }

        if ($this->hasPath()) {
            return $this->getStorage()->has();
        }

        if ($this->hasStream()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasLocal()
    {
        return strlen($this->getLocal()) > 0;
    }


    /**
     * @return bool
     */
    public function hasPath()
    {
        return strlen($this->getPath()) > 0;
    }

    /**
     * @return bool
     * @throws FileException
     */
    public function hasStream()
    {
        return is_resource($this->getStream());
    }


    /**
     * @return bool|false|mixed|resource|null
     * @throws Exception\AdapterException
     * @throws FileException
     * @throws FileNotFoundException
     */
    public function getStream()
    {
        if (is_resource($this->_stream)) {
            return $this->_stream;
        }

        if ($this->getStorage() instanceof Storage && $this->getStorage()->has()) {
            return $this->getStorage()->readStream();
        }

        if (file_exists($this->getPath())) {
            return fopen($this->getPath(), 'r+');
        }

        throw new FileException("Stream is not founded");
    }


    /**
     * @param $stream
     * @return bool|false|mixed|resource|null
     * @throws Exception\AdapterException
     * @throws FileException
     * @throws FileNotFoundException
     */
    function setStream($stream)
    {
        $this->_stream = $stream;
        return $this->getStream();
    }


    /**
     * @param Storage|null $storage
     * @return Storage|null
     * @throws Exception\AdapterException
     * @throws FileException
     * @throws FileNotFoundException
     */
    public function stored(Storage $storage = null): ?Storage
    {

        if (strlen($this->getPath()) == 0 && is_resource($this->getStream())) {
            throw new FileException("File is a stream");
        }

        if (strlen($this->getPath()) == 0) {
            throw new FileException("The path to the file required for stored file");
        }

        if ($storage == null) {
            $storage = $this->getStorage();
        }

        $storage = $this->initStorage($storage);

        if (!$storage->exists($this->getPath())) {
            throw new FileException("File is not founded");
        }

        return $this->getStorage();
    }


    /**
     * @param Storage|null $storage
     * @return Storage|null
     * @throws Exception\AdapterException
     */
    public function to(Storage $storage = null): ?Storage
    {
        if ($storage === null) {
            $storage = Storage::getLocalStorage();
        }

        $storage->setFile($this);

        return $storage;
    }


    /**
     * @return bool|false|mixed|string
     * @throws FileException
     * @throws FileNotFoundException
     */
    public function getMimetype()
    {
        if ($this->hasLocal()) {
            return mime_content_type($this->getLocal());
        }

        if ($this->getStorage() instanceof Storage) {
            return $this->getStorage()->getMimetype();
        }

        if (strlen($this->getExtension()) > 0) {
            return MimeTypeHelper::getMimeTypeByExt("." . $this->getExtension());
        }

        throw new FileException("No data");
    }


    /**
     * @return mixed|string
     * @throws Exception\AdapterException
     * @throws FileException
     * @throws FileNotFoundException
     */
    public function getExtension()
    {
        if ($this->hasLocal()) {
            return pathinfo($this->getLocal())['extension'];
        }
        if ($this->hasPath()) {
            return pathinfo($this->getPath())['extension'];
        }

        if ($this->hasStream()) {
            return @pathinfo(stream_get_meta_data($this->getStream())['uri'])['extension'];
        }

        throw new FileException("No data");
    }


    /**
     * @param null $source
     * @return bool|false|int|mixed|string
     * @throws FileNotFoundException
     */
    public function getTimestamp($source = null)
    {
        if ($this->hasLocal()) {
            return filemtime($this->getLocal());
        }

        if ($this->getStorage() instanceof Storage) {
            return $this->getStorage()->getTimestamp();
        }

        return time();
    }


    /**
     * @param null $source
     * @return bool|false|int|mixed
     * @throws Exception\AdapterException
     * @throws FileException
     * @throws FileNotFoundException
     */
    public function getSize($source = null)
    {
        if ($this->hasLocal()) {
            return filesize($this->getLocal());
        }
        if ($this->getStorage() instanceof Storage) {
            return $this->getStorage()->getSize();
        }
        if ($this->hasStream()) {
            return fstat($this->getStream())['size'];
        }
        throw new FileException("No data");
    }


    /**
     * @return string
     * @throws Exception\AdapterException
     * @throws FileException
     * @throws FileNotFoundException
     */
    public function getBasename()
    {
        if ($this->hasLocal()) {
            return basename($this->getLocal());
        }
        if ($this->hasPath()) {
            return basename($this->getPath());
        }

        if ($this->hasStream()) {
            return @basename(stream_get_meta_data($this->getStream())['uri']);
        }

        throw new FileException("No data");
    }


    /**
     * @return string
     * @throws Exception\AdapterException
     * @throws FileException
     * @throws FileNotFoundException
     */
    public function getDirname()
    {
        if ($this->hasLocal()) {
            return dirname($this->getLocal());
        }
        if ($this->hasPath()) {
            return dirname($this->getPath());
        }

        if ($this->hasStream()) {
            return @dirname(stream_get_meta_data($this->getStream())['uri']);
        }

        throw new FileException("No data");
    }


    /**
     * @return mixed|string
     * @throws Exception\AdapterException
     * @throws FileException
     * @throws FileNotFoundException
     */
    public function getFilename()
    {
        if ($this->hasLocal()) {
            return pathinfo($this->getLocal())['filename'];
        }
        if ($this->hasPath()) {
            return pathinfo($this->getPath())['filename'];
        }

        if ($this->hasStream()) {
            return @pathinfo(stream_get_meta_data($this->getStream())['uri'])['filename'];
        }

        throw new FileException("No data");
    }


    /**
     * @return bool
     * @throws \Exception
     */
    public function hasStorage()
    {
        return $this->getStorage() instanceof Storage;
    }
}
