<?php


namespace Yiisoft\files\Tests;


use PHPUnit\Framework\TestCase;
use Yiisoft\Files\Adapter\AdapterFactory;
use Yiisoft\Files\Adapter\LocalAdapter;
use Yiisoft\Files\Dto\LocalAdapterDTO;

class AdapterTest extends TestCase
{

    /**
     * @param $dto
     * @param $adapterClass
     * @throws \Exception
     * @dataProvider getTestFactory
     */
    public function testFactory($dto, $adapterClass)
    {
        $adapter = AdapterFactory::create($dto);
        $this->assertInstanceOf($adapterClass, $adapter);
    }

    /**
     * @return array
     */
    public function getTestFactory()
    {
        $data = [];

        $localDTO = new LocalAdapterDTO();
        $localDTO->root = "/test/static";
        $data[] = [$localDTO, LocalAdapter::class];

        return $data;
    }
}
