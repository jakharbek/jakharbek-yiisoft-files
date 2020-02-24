<?php


namespace Yiisoft\Files\Traits;


use League\Flysystem\Filesystem;
use Yiisoft\Files\Exception\FileException;
use Yiisoft\Files\File;
use Yiisoft\Files\Storage;

/**
 * Trait StorageTrait
 * @package Yiisoft\files
 *
 * @method void isFile
 * @method File getFile
 * @method Storage getStorage
 * @method Filesystem getFilesystem
 */
trait StorageTrait
{

    /**
     * @param $path
     * @param null $file
     * @param array $config
     * @return bool
     * @throws FileException
     * @throws \League\Flysystem\FileExistsException
     */
    public function write($path, $file = null, $config = [])
    {
        if ($file == null) {
            $this->isFile();
            $file = $this->getFile();
        }

        /**
         * @var $file string|File|resource
         */
        if (is_a($file, File::class)) {
            $fileObject = $file;
            if (!$fileObject->exists()) {
                throw new FileException('File is not exists');
            }
            $file = $file->getStorage()->readStream();
        }
        if (is_resource($file)) {
            return $this->getFilesystem()->writeStream($path, $file, $config);
        }
        if (is_string($file)) {
            return $this->getFilesystem()->write($path, $file, $config);
        }
    }


    /**
     * @param null $source
     * @return bool|false|mixed|resource
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function readStream($source = null)
    {
        if ($source == null) {
            $this->isFile();
            $source = $this->getFile()->getSource();
        }
        return $this->getFilesystem()->readStream($source);
    }


    /**
     * @param null $source
     * @return bool
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function delete($source = null)
    {
        if ($source == null) {
            $this->isFile();
            $source = $this->getFile()->getSource();
        }
        return $this->getFilesystem()->delete($source);
    }

    /**
     * @param null $source
     * @return bool|false|mixed|string
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function readAndDelete($source = null)
    {
        if ($source == null) {
            $this->isFile();
            $source = $this->getFile()->getSource();
        }
        return $this->getFilesystem()->readAndDelete($source);
    }

    /**
     * @param $to
     * @param null $from
     * @return bool
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function rename($to, $from = null)
    {
        if ($from == null) {
            $this->isFile();
            $from = $this->getFile()->getSource();
        }
        return $this->getFilesystem()->rename($from, $to);
    }

    /**
     * @param $to
     * @param null $from
     * @return bool
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function copy($to, $from = null)
    {
        if ($from == null) {
            $this->isFile();
            $from = $this->getFile()->getSource();
        }
        return $this->getFilesystem()->copy($from, $to);
    }

    /**
     * @param null $source
     * @return mixed
     */
    public function exists($source = null)
    {
        if ($source == null) {
            $this->isFile();
            $source = $this->getFile()->getSource();
        }

        return $this->has($source);
    }


    /**
     * @param $file
     * @param null $dist
     * @param array $config
     * @return bool
     * @throws FileException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function update($file, $dist = null, $config = [])
    {
        if ($dist == null) {
            $this->isFile();
            $dist = $this->getFile()->getSource();
        }

        /**
         * @var $file string|File|resource
         */
        if (is_a($file, File::class)) {
            $fileObject = $file;
            if (!$fileObject->exists()) {
                throw new FileException('File is not exists');
            }
            $file = $file->getStorage()->readStream();
        }
        if (is_resource($file)) {
            return $this->getFilesystem()->updateStream($dist, $file, $config);
        }
        if (is_string($file)) {
            return $this->getFilesystem()->update($dist, $file, $config);
        }
    }

    /**
     * @param $dist
     * @param null $file
     * @param array $config
     * @return bool
     * @throws FileException
     */
    public function put($dist, $file = null, $config = [])
    {
        if ($file == null) {
            $this->isFile();
            $file = $this->getFile();
        }

        /**
         * @var $file string|File|resource
         */
        if (is_a($file, File::class)) {
            $fileObject = $file;
            if (!$fileObject->exists()) {
                throw new FileException('File is not exists');
            }
            $file = $file->getStorage()->readStream();
        }

        if (is_resource($file)) {
            return $this->getFilesystem()->putStream($dist, $file, $config);
        }
        if (is_string($file)) {
            return $this->getFilesystem()->put($dist, $file, $config);
        }
    }

    /**
     * @param null $source
     * @return bool|false|mixed|string
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function read($source = null)
    {
        if ($source == null) {
            $this->isFile();
            $source = $this->getFile()->getSource();
        }
        return $this->getFilesystem()->read($source);
    }


    /**
     * @param null $source
     * @return bool|false|mixed|string
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getMimetype($source = null)
    {
        if ($source == null) {
            $this->isFile();
            $source = $this->getFile()->getSource();
        }
        return $this->getFilesystem()->getMimetype($source);
    }

    /**
     * @param null $source
     * @return bool|false|mixed|string
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getTimestamp($source = null)
    {
        if ($source == null) {
            $this->isFile();
            $source = $this->getFile()->getSource();
        }
        return $this->getFilesystem()->getTimestamp($source);
    }

    /**
     * @param null $source
     * @return bool|false|int
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getSize($source = null)
    {
        if ($source == null) {
            $this->isFile();
            $source = $this->getFile()->getSource();
        }
        return $this->getFilesystem()->getSize($source);
    }

    /**
     * @param $dist
     * @return bool
     */
    public function createDir($dist)
    {
        return $this->getFilesystem()->createDir($dist);
    }

    /**
     * @param $dist
     * @return bool
     */
    public function deleteDir($dist)
    {
        return $this->getFilesystem()->deleteDir($dist);
    }

    /**
     * @param $dist
     * @param bool $recursive
     * @return array
     */
    public function listContents($dist, $recursive = false)
    {
        return $this->getFilesystem()->listContents($dist, $recursive);
    }
}
