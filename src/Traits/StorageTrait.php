<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */

namespace Yiisoft\Files\Traits;


use League\Flysystem\Adapter\Local;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Yiisoft\Files\Dto\LocalAdapterDTO;
use Yiisoft\Files\Exception\FileException;
use Yiisoft\Files\File;
use Yiisoft\Files\Helper\ConfigHelper;
use Yiisoft\Files\Helper\StorageHelper;
use Yiisoft\Files\Storage;

/**
 * Trait StorageTrait
 * @package Yiisoft\files
 *
 * @method void hasFile
 * @method File getFile
 * @method Storage getTo
 * @method Storage getStorage
 * @method Filesystem getFilesystem
 */
trait StorageTrait
{

    /**
     * @param null $file
     * @return Storage
     * @throws \Yiisoft\Files\Exception\AdapterException
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
     * @param $dist
     * @param null $file
     * @param array $config
     * @return Storage|null
     * @throws FileException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     */
    public function write($dist = null, $file = null, $config = []): ?Storage
    {
        if ($dist == null) {
            $dist = $this->getFile()->getBasename();
        }

        $dist = $this->getDistWithTemplate($dist);

        if ($file == null) {
            $this->hasFile();
            $file = $this->getFile();

            if (!$this->isSourceStorage()) {
                return $this->getFile()->getStorage()->write($dist, $file, $config);
            }
        }


        /**
         * @var $file string|File|resource
         */
        if (is_a($file, File::class)) {
            $file = $file->getStream();
        }

        if (is_resource($file)) {
            if (!$this->getFilesystem()->writeStream($dist, $file, $config)) {
                throw new FileException("File is not written");
            }
            return File::from($dist, $this)->getStorage();
        }

        if (is_string($file)) {
            if (!$this->getFilesystem()->write($dist, $file, $config)) {
                throw new FileException("File is not written");
            }
            return File::from($dist, $this)->getStorage();
        }

        throw new FileException('File is not exists');
    }

    /**
     * @param $dist
     * @return string
     */
    public function getDistWithTemplate($dist)
    {
        if (!$this->hasTemplate()) {
            return $dist;
        }

        return StorageHelper::getPathFromTemplate($dist, $this->getTemplate());
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isSourceStorage(): bool
    {
        if (!$this->getFile() instanceof File) {
            return false;
        }

        if (!($this->getFile()->getStorage() instanceof Storage)) {
            return false;
        }

        return $this->isEqual($this->getFile()->getStorage());
    }

    /**
     * @param Storage $storage
     * @return bool
     */
    public function isEqual(Storage $storage): bool
    {
        return $this === $storage;
    }

    /**
     * @param null $source
     * @throws FileException
     * @throws FileNotFoundException
     */
    public function delete($source = null): void
    {
        if ($source == null) {
            $this->hasFile();
            $source = $this->getFile()->getPath();

            if (!$this->isSourceStorage()) {
                $this->getFile()->getStorage()->delete($source);
            }
        }

        if (!$this->getFilesystem()->delete($source)) {
            throw new FileException("File is not deleted");
        }
    }

    /**
     * @param $to
     * @param null $from
     * @return Storage|null
     * @throws FileException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     */
    public function rename($to, $from = null): ?Storage
    {

        if ($from == null) {
            $this->hasFile();
            $from = $this->getFile()->getPath();

            if (!preg_match('/[\/\\\\]/', $to)) {
                $to = $this->getFile()->getDirname() . "/" . $to;
            }

            if (!$this->isSourceStorage()) {
                return $this->getFile()->getStorage()->rename($to, $from);
            }
        }

        if ($this->has($to)) {
            throw new FileException("File already exists at path: " . $to);
        }

        if (!preg_match('/[\/\\\\]/', $to)) {
            $to = dirname($from) . "/" . $to;
        }

        if (!$this->getFilesystem()->rename($from, $to)) {
            throw new FileException("File not changed");
        }

        return File::from($to, $this)->getStorage();
    }

    /**
     * @param $to
     * @param null $from
     * @return Storage|null
     * @throws FileException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     */
    public function copy($to, $from = null): ?Storage
    {
        if ($from == null) {
            $this->hasFile();
            $from = $this->getFile()->getPath();

            if (!preg_match('/[\/\\\\]/', $to)) {
                $to = $this->getFile()->getDirname() . "/" . $to;
            }

            if (!$this->isSourceStorage()) {
                return $this->getFile()->getStorage()->copy($to, $from);
            }
        }

        if (!preg_match('/[\/\\\\]/', $to)) {
            $to = dirname($from) . "/" . $to;
        }

        if (!$this->getFilesystem()->copy($from, $to)) {
            throw new FileException("File is not copied");
        }

        return File::from($to, $this)->getStorage();
    }

    /**
     * @param null $source
     * @return bool
     * @throws \Exception
     */
    public function exists($source = null): bool
    {
        if ($source == null) {
            $this->hasFile();
            $source = $this->getFile()->getPath();

            if (!$this->isSourceStorage()) {
                return $this->getFile()->getStorage()->exists($source);
            }
        }

        return $this->has($source);
    }

    /**
     * @param $file
     * @param null $dist
     * @param array $config
     * @return Storage|null
     * @throws FileException
     * @throws FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     */
    public function update($file, $dist = null, $config = []): ?Storage
    {
        if ($dist == null) {
            $this->hasFile();
            $dist = $this->getFile()->getPath();
        }

        /**
         * @var $file string|File|resource
         */
        if (is_a($file, File::class)) {
            if (!$file->exists()) {
                throw new FileException('File is not exists');
            }
            $file = $file->getStream();
        }

        if (is_resource($file)) {
            if (!$this->getFilesystem()->updateStream($dist, $file, $config)) {
                throw new FileException("File is not updated");
            }
            return File::from($dist, $this)->getStorage();
        }
        if (is_string($file)) {
            if (!$this->getFilesystem()->update($dist, $file, $config)) {
                throw new FileException("File is not updated");
            }
            return File::from($dist, $this)->getStorage();
        }

        throw new FileException("File source is not founded");
    }

    /**
     * @param null $source
     * @return bool|false|mixed|resource
     * @throws FileNotFoundException
     */
    public function readStream($source = null)
    {
        if ($source == null) {
            $this->hasFile();
            $source = $this->getFile()->getPath();
            if (!$this->isSourceStorage()) {
                return $this
                    ->getFile()
                    ->getStorage()
                    ->readStream($source);
            }
        }

        return $this->getFilesystem()->readStream($source);
    }

    /**
     * @param null $dist
     * @param null $file
     * @param array $config
     * @return Storage|null
     * @throws FileException
     * @throws FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     */
    public function put($dist = null, $file = null, $config = []): ?Storage
    {
        if ($dist == null) {
            $dist = $this->getFile()->getBasename();
        }

        $dist = $this->getDistWithTemplate($dist);

        if ($file == null) {
            $this->hasFile();
            $file = $this->getFile();
        }


        if ($file instanceof File) {
            $file = $file->getStream();
        }

        if (is_resource($file)) {
            if (!$this->getFilesystem()->putStream($dist, $file, $config)) {
                throw new FileException("File is not putted");
            }
            return File::from($dist, $this)->getStorage();
        }
        if (is_string($file)) {
            if (!$this->getFilesystem()->put($dist, $file, $config)) {
                throw new FileException("File is not putted");
            }
            return File::from($dist, $this)->getStorage();
        }

        throw new FileException('File is not exists');
    }

    /**
     * @param null $source
     * @return bool|false|mixed|string
     * @throws FileNotFoundException
     */
    public function read($source = null)
    {
        if ($source == null) {
            $this->hasFile();
            $source = $this->getFile()->getPath();
            if (!$this->isSourceStorage()) {
                return $this
                    ->getFile()
                    ->getStorage()
                    ->read($source);
            }
        }

        return $this->getFilesystem()->read($source);
    }

    /**
     * @param null $source
     * @return bool|false|mixed|string
     * @throws FileNotFoundException
     */
    public function getMimetype($source = null)
    {
        if ($source == null) {
            $this->hasFile();
            $source = $this->getFile()->getPath();
            if (!$this->isSourceStorage()) {
                return $this
                    ->getFile()
                    ->getStorage()
                    ->getMimetype($source);
            }
        }
        return $this->getFilesystem()->getMimetype($source);
    }

    /**
     * @param null $source
     * @return bool|false|mixed|string
     * @throws FileNotFoundException
     */
    public function getTimestamp($source = null)
    {
        if ($source == null) {
            $this->hasFile();
            $source = $this->getFile()->getPath();
            if (!$this->isSourceStorage()) {
                return $this
                    ->getFile()
                    ->getStorage()
                    ->getTimestamp($source);
            }
        }


        return $this->getFilesystem()->getTimestamp($source);
    }

    /**
     * @param null $source
     * @return bool|false|int
     * @throws FileNotFoundException
     */
    public function getSize($source = null)
    {
        if ($source == null) {
            $this->hasFile();
            $source = $this->getFile()->getPath();
            if (!$this->isSourceStorage()) {
                return $this
                    ->getFile()
                    ->getStorage()
                    ->getSize($source);
            }
        }
        return $this->getFilesystem()->getSize($source);
    }

    /**
     * @param $dist
     * @return $this
     * @throws FileException
     */
    public function createDir($dist)
    {
        if (!$this->getFilesystem()->createDir($dist)) {
            throw new FileException("Dir is not created");
        }

        return $this;
    }

    /**
     * @param $dist
     * @return $this
     * @throws FileException
     */
    public function deleteDir($dist)
    {
        if (!$this->getFilesystem()->deleteDir($dist)) {
            throw new FileException("Dir is not deleted");
        }

        return $this;
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

    /**
     * @param $dist
     * @param null $resource
     * @param array $config
     * @return Storage|null
     * @throws FileException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     */
    public function writeStream($dist, $resource = null, $config = []): ?Storage
    {
        if (!$this->isSourceStorage() && $resource == null) {
            $resource = $this->getFile()->getStream();
        }

        if (!$this->getFilesystem()->writeStream($dist, $resource, $config)) {
            throw new FileException("Stream is not written");
        }

        return File::from($dist, $this)->getStorage();
    }

    /**
     * @param $dist
     * @param null $resource
     * @param array $config
     * @return Storage|null
     * @throws FileException
     * @throws FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     */
    public function updateStream($dist, $resource = null, $config = []): ?Storage
    {
        if (!$this->isSourceStorage() && $resource == null) {
            $resource = $this->getFile()->getStream();
        }

        if (!$this->getFilesystem()->updateStream($dist, $resource, $config)) {
            throw new FileException("Stream is not written");
        }

        return File::from($dist, $this)->getStorage();
    }


}
