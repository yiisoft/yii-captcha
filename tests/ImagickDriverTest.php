<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yiiunit\captcha;

use yii\captcha\ImagickDriver;

class ImagickDriverTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!extension_loaded('imagick') || !in_array('PNG', (new \Imagick())->queryFormats('PNG'), true)) {
            $this->markTestSkipped('imagick extension is missing.');
        }

        parent::setUp();
    }

    public function testRenderImage()
    {
        $driver = new ImagickDriver();
        $driver->width = 222;
        $driver->height = 111;

        $imageBinary = $driver->renderImage('test');
        $this->assertNotEmpty($imageBinary);

        $imagick = new \Imagick();
        $imagick->readImageBlob($imageBinary);

        $this->assertEquals($driver->width, $imagick->getImageWidth());
        $this->assertEquals($driver->height, $imagick->getImageHeight());
        $this->assertEquals($driver->getImageMimeType(), $imagick->getImageMimeType());
    }
}