<?php


namespace Yiisoft\Files\Tests;


use PHPUnit\Framework\TestCase;
use Yiisoft\Files\Helper\AdapterHelper;

class ValidationTest extends TestCase
{

    /**
     * @dataProvider getValidateValid
     */
    public function testValidateValid($attr, $attributes)
    {
        try {
            AdapterHelper::validate($attr, $attributes);
            $result = true;
        } catch (\Exception $exception) {
            $result = false;
        }
        $this->assertTrue($result);
    }

    public function getValidateValid()
    {
        return [
            [
                'file', ['file' => 1]
            ],
            [
                'src', ['src' => '/app']
            ],
            [
                'perm', ['perm' => [123, 321]]
            ]
        ];
    }

    /**
     * @dataProvider getValidateError
     */
    public function testValidateError($attr, $attributes)
    {
        try {
            AdapterHelper::validate($attr, $attributes);
            $result = false;
        } catch (\Exception $exception) {
            $result = true;
        }
        $this->assertTrue($result);
    }

    public function getValidateError()
    {
        return [
            [
                'file', ['data' => 123]
            ],
            [
                'src', []
            ],
            [
                'perm', [123]
            ]
        ];
    }

    /**
     * @param $requiredAttributes
     * @param $attributes
     * @dataProvider getValidationValid
     */
    public function testValidationValid($requiredAttributes, $attributes)
    {
        try {
            AdapterHelper::validation($requiredAttributes, $attributes);
            $result = true;
        } catch (\Exception $exception) {
            $result = false;
        }
        $this->assertTrue($result);
    }

    public function getValidationValid()
    {
        return [
            [
                ['file', 'src', 'perm'], ['file' => 'file.jpg', 'src' => '/static', 'perm' => [777, 775]]
            ]
        ];
    }

    /**
     * @param $requiredAttributes
     * @param $attributes
     * @dataProvider getValidationError
     */
    public function testValidationError($requiredAttributes, $attributes)
    {
        try {
            AdapterHelper::validation($requiredAttributes, $attributes);
            $result = false;
        } catch (\Exception $exception) {
            $result = true;
        }
        $this->assertTrue($result);
    }

    public function getValidationError()
    {
        return [
            [
                ['file', 'src', 'perm'], ['folders' => 'file.jpg', 'src' => '/static', 'perm' => [777, 775]]
            ]
        ];
    }
}
