<?php

declare(strict_types=1);
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Captcha;

use yii\base\Application;
use yii\base\Component;
use yii\exceptions\InvalidConfigException;

/**
 * Driver is the base class for CAPTCHA rendering driver classes.
 *
 * By configuring the properties of Driver, you may customize the appearance of
 * the generated CAPTCHA images, such as the font color, the background color, etc.
 */
abstract class Driver extends Component implements DriverInterface
{
    use VerifyCodeGeneratorTrait;

    /**
     * @var int the width of the generated CAPTCHA image. Defaults to 120.
     */
    public $width = 120;
    /**
     * @var int the height of the generated CAPTCHA image. Defaults to 50.
     */
    public $height = 50;
    /**
     * @var int padding around the text. Defaults to 2.
     */
    public $padding = 2;
    /**
     * @var int the offset between characters. Defaults to -2. You can adjust this property
     * in order to decrease or increase the readability of the captcha.
     */
    public $offset = -2;
    /**
     * @var int the background color. For example, 0x55FF00.
     * Defaults to 0xFFFFFF, meaning white color.
     */
    public $backColor = 0xFFFFFF;
    /**
     * @var int the font color. For example, 0x55FF00. Defaults to 0x2040A0 (blue color).
     */
    public $foreColor = 0x2040A0;
    /**
     * @var bool whether to use transparent background. Defaults to false.
     */
    public $transparent = false;

    /**
     * @var string|null the TrueType font file. This can be either a file path or [path alias](guide:concept-aliases).
     */
    protected $fontFile;

    /**
     * @var Application
     */
    protected $app;

    /**
     * Driver constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return string|null
     */
    public function getFontFile(): string
    {
        if ($this->fontFile === null) {
            $this->setFontFile('@Yiisoft/Yii/Captcha/SpicyRice.ttf');
        }

        return $this->fontFile;
    }

    /**
     * @param string $fontFile
     *
     * @return Driver
     */
    public function setFontFile(string $fontFile): self
    {
        $this->fontFile = $this->app->getAlias($fontFile);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImageMimeType(): string
    {
        $file = $this->getFontFile();

        if (!is_file($file)) {
            throw new InvalidConfigException("The font file does not exist: {$file}");
        }

        return 'image/png';
    }
}
