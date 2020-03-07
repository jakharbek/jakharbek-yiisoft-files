<?php


namespace Yiisoft\files\Tests;


use PHPUnit\Framework\TestCase;
use Yiisoft\Files\File;
use Yiisoft\Files\Helper\MimeTypeHelper;
use Yiisoft\Files\Storage;

class FileTest extends TestCase
{
    /**
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     * @throws \Yiisoft\Files\Exception\FileException
     */
    public function testLocal()
    {
        $file = File::local(__DIR__ . "/data/static-file.txt");
        $this->assertTrue($file->exists());
        $this->assertEquals(MimeTypeHelper::getMimeTypeByExt(".txt"), $file->getMimetype());
        $this->assertEquals("txt", $file->getExtension());
        $this->assertIsInt($file->getTimestamp());
        $this->assertIsInt($file->getSize());
        $this->assertEquals("static-file.txt", $file->getBasename());
        $this->assertEquals(__DIR__ . "/data", $file->getDirname());
        $this->assertEquals("static-file", $file->getFilename());

        $rename = $file->rename("static-file-1.txt");
        $this->assertTrue($rename->exists());

        $rename = $rename->rename("static-file.txt");
        $this->assertTrue($rename->exists());

        $copy = $file->copy("static-file-2.txt");
        $this->assertTrue($copy->exists());
        $copy->delete();

        $file->to()->put("static-file-4.txt")->delete();

        $this->assertTrue(is_a($file->to(), Storage::class));

        $newFile = $file->to()->put();
        $newFile->copy('static-file-3.txt');
        $newFile->delete();

        $this->assertFalse($copy->exists());

        $file = File::local(__DIR__ . "/data/static-file.txt")->to()->setTemplate("/:year/:month/:day/:hour/:minute/:second/")->put();
    }

    /**
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     * @throws \Yiisoft\Files\Exception\FileException
     */
    public function testFrom()
    {
        $file = File::from('static-file.txt');
        $this->assertTrue($file->exists());
        $this->assertEquals(MimeTypeHelper::getMimeTypeByExt(".txt"), $file->getMimetype());
        $this->assertEquals("txt", $file->getExtension());
        $this->assertIsInt($file->getTimestamp());
        $this->assertIsInt($file->getSize());
        $this->assertEquals("static-file.txt", $file->getBasename());
        $this->assertIsString($file->getDirname());
        $this->assertEquals("static-file", $file->getFilename());

        $rename = $file->rename("static-file-1.txt");
        $this->assertTrue($rename->exists());

        $rename = $rename->rename("static-file.txt");
        $this->assertTrue($rename->exists());

        $copy = $file->copy("static-file-2.txt");
        $this->assertTrue($copy->exists());
        $copy->delete();
        File::from('static-file.txt')->to()->setTemplate("/:year/:month/:day/:hour/:minute/:second/")->put();
        File::from('static-file.txt')->to()->put()->delete();
    }


    /**
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     * @throws \Yiisoft\Files\Exception\FileException
     */
    public function testStream()
    {
        $file = File::stream(fopen(__DIR__ . "/data/static-file.txt", "r+"));
        $this->assertTrue($file->exists());
        $this->assertEquals(MimeTypeHelper::getMimeTypeByExt(".txt"), $file->getMimetype());
        $this->assertEquals("txt", $file->getExtension());
        $this->assertIsInt($file->getTimestamp());
        $this->assertIsInt($file->getSize());
        $this->assertEquals("static-file.txt", $file->getBasename());
        $this->assertIsString($file->getDirname());
        $this->assertEquals("static-file", $file->getFilename());
        File::stream(fopen(__DIR__ . "/data/static-file.txt", "r+"))->to()->put()->delete();
        File::stream(fopen(__DIR__ . "/data/file1.txt", "r+"))->to()->setTemplate("/:year/:month/:day/:hour/:minute/:second/")->put();

    }

    /**
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \Yiisoft\Files\Exception\AdapterException
     * @throws \Yiisoft\Files\Exception\FileException
     */
    public function testForm()
    {

        $_FILES = [
            'document' => [
                'name' => 'static-file.txt',
                'type' => 'text/plain',
                'tmp_name' => __DIR__ . "/data/static-file.txt",
                'error' => UPLOAD_ERR_OK,
                'size' => 98174
            ],
        ];
        $file = File::form("document");
        $this->assertTrue($file->exists());
        $this->assertEquals(MimeTypeHelper::getMimeTypeByExt(".txt"), $file->getMimetype());
        $this->assertEquals("txt", $file->getExtension());
        $this->assertIsInt($file->getTimestamp());
        $this->assertIsInt($file->getSize());
        $this->assertEquals("static-file.txt", $file->getBasename());
        $this->assertEquals(__DIR__ . "/data", $file->getDirname());
        $this->assertEquals("static-file", $file->getFilename());

        $rename = $file->rename("static-file-1.txt");
        $this->assertTrue($rename->exists());

        $rename = $rename->rename("static-file.txt");
        $this->assertTrue($rename->exists());

        $copy = $file->copy("static-file-2.txt");
        $this->assertTrue($copy->exists());
        $copy->delete();

        $file->to()->put("static-file-4.txt")->delete();

        $this->assertTrue(is_a($file->to(), Storage::class));

        $newFile = $file->to()->put();
        $newFile->copy('static-file-3.txt');
        $newFile->delete();

        File::local(__DIR__ . "/data/file2.txt")->to()->setTemplate("/:year/:month/:day/:hour/:minute/:second/")->put();

        $this->assertFalse($copy->exists());

    }
}
