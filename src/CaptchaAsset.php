<?php

declare(strict_types=1);
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Captcha;

use yii\web\AssetBundle;

/**
 * This asset bundle provides the javascript files needed for the [[Captcha]] widget.
 */
class CaptchaAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@Yiisoft/Yii/Captcha/assets';
    /**
     * {@inheritdoc}
     */
    public $js = ['yii.captcha.js'];
}
