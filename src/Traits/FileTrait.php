<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2020 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * @author Jakharbek <jakharbek@gmail.com>
 */

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
     * @param $to
     * @return File|null
     * @throws FileException
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     */
    public function rename($to): ?File
    {
        if (!preg_match('/[\/\\\\]/', $to)) {
            $to = $this->getDirname() . "/" . $to;
        }

        if ($this->hasLocal()) {
            $from = $this->getLocal();
            if (!rename($from, $to)) {
                throw new FileException("File is not renamed");
            }
            return File::local($to);
        }

        if ($this->hasStorage()) {
            return $this->getStorage()->rename($to)->getFile();
        }

        if ($this->hasStream()) {
            throw new FileException("Its is not file, it is stream");
        }

        throw new FileException("It is not possible to rename");
    }

    /**
     * @throws FileException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function delete()
    {
        if ($this->hasLocal()) {
            $from = $this->getLocal();
            if (!unlink($from)) {
                throw new FileException("File is not deleted");
            }
            return;
        }

        if ($this->hasStorage()) {
            return $this->getStorage()->delete();
        }

        if ($this->hasStream()) {
            throw new FileException("Its is not file, it is stream");
        }

        throw new FileException("It is not possible to delete");
    }

    /**
     * @param $to
     * @return File|null
     * @throws FileException
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     */
    public function copy($to)
    {
        if (!preg_match('/[\/\\\\]/', $to)) {
            $to = $this->getDirname() . "/" . $to;
        }

        if ($this->hasLocal()) {
            $from = $this->getLocal();
            if (!copy($from, $to)) {
                throw new FileException("File is not copied");
            }
            return File::local($to);
        }

        if ($this->hasStorage()) {
            return $this->getStorage()->copy($to)->getFile();
        }

        if ($this->hasStream()) {
            throw new FileException("Its is not file, it is stream");
        }

        throw new FileException("It is not possible to copy");
    }


    /**
     * @param Storage|null $storage
     * @return mixed
     */
    public function to(Storage $storage = null)
    {
        return $this->getFile()->to($storage);
    }
}
