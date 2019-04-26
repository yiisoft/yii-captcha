<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Captcha\Tests;

use Yiisoft\Yii\Captcha\Driver;
use yii\tests\TestCase;

class VerifyCodeGeneratorTraitTest extends TestCase
{
    public function testGenerateVerifyCode()
    {
        $this->mockWebApplication();

        /* @var $driver Driver */
        $driver = $this->getMockBuilder(Driver::class)
            ->setConstructorArgs([$this->app])
            ->getMockForAbstractClass();

        $this->assertNotEmpty($driver->generateVerifyCode());

        $driver->minLength = 10;
        $driver->maxLength = 10;
        $this->assertEquals(10, strlen($driver->generateVerifyCode()));
    }
}
