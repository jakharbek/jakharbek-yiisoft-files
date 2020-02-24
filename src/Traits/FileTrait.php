<?php

namespace Yiisoft\Files\Traits;


use Yiisoft\Files\Exception\FileException;
use Yiisoft\Files\File;
use Yiisoft\Files\Storage;

/**
 * Trait FileTrait
 * @package Yiisoft\files
 *
 * @method Storage getStorage
 * @method resource getStream
 * @see File::getStorage()
 * @see File::getStream()
 */
trait FileTrait
{

    /**
     * @param $file resource|string|File
     * @return mixed
     */
    public function update($file)
    {
        return $this->getStorage()->update($file);
    }


    /**
     * @param $dist
     * @param Storage|null $storage
     * @param array $config
     * @return bool
     */
    public function write($dist, Storage $storage = null, $config = [])
    {
        if ($storage == null) {
            $storage = $this->getStorage();
        }

        return $storage->write($dist, $this->getStream(), $config);
    }


    /**
     * @return bool
     * @throws FileException
     */
    public function delete()
    {
        $storage = $this->getStorage();
        return $storage->delete();
    }


    /**
     * @return bool|false|mixed|string
     */
    public function readAndDelete()
    {
        return $this->getStorage()->readAndDelete();
    }


    /**
     * @param $to
     * @return bool
     */
    public function rename($to)
    {
        return $this->getStorage()->rename($to);
    }


    /**
     * @param $to
     * @return bool
     */
    public function copy($to)
    {
        return $this->getStorage()->copy($to);
    }

    /**
     * @param null $dist
     * @param Storage|null $storage
     * @param array $config
     * @return mixed
     */
    public function put($dist = null, Storage $storage = null, $config = [])
    {
        if ($storage == null) {
            $storage = $this->getStorage();
        }

        return $storage->put($dist, $this, $config);
    }

}
