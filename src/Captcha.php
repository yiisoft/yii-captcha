<?php

declare(strict_types=1);
/**
 * @link http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Captcha;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\InputWidget;

/**
 * Captcha renders a CAPTCHA image and an input field that takes user-entered verification code.
 *
 * Captcha is used together with [[CaptchaAction]] to provide [CAPTCHA](http://en.wikipedia.org/wiki/Captcha) - a way
 * of preventing website spamming.
 *
 * The image element rendered by Captcha will display a CAPTCHA image generated by
 * an action whose route is specified by [[captchaAction]]. This action must be an instance of [[CaptchaAction]].
 *
 * When the user clicks on the CAPTCHA image, it will cause the CAPTCHA image
 * to be refreshed with a new CAPTCHA.
 *
 * You may use [[\Yiisoft\Yii\Captcha\CaptchaValidator]] to validate the user input matches
 * the current CAPTCHA verification code.
 *
 * The following example shows how to use this widget with a model attribute:
 *
 * ```php
 * echo Captcha::widget([
 *     'model' => $model,
 *     'attribute' => 'captcha',
 * ]);
 * ```
 *
 * The following example will use the name property instead:
 *
 * ```php
 * echo Captcha::widget([
 *     'name' => 'captcha',
 * ]);
 * ```
 *
 * You can also use this widget in an [[\yii\widgets\ActiveForm|ActiveForm]] using the [[\yii\widgets\ActiveField::widget()|widget()]]
 * method, for example like this:
 *
 * ```php
 * <?= $form
 *     ->field($model, 'captcha')
 *     ->widget(\Yiisoft\Yii\Captcha\Captcha::class, [
 *     // configure additional widget properties here
 * ]) ?>
 * ```
 */
class Captcha extends InputWidget
{
    /**
     * @var array|string the route of the action that generates the CAPTCHA images.
     * The action represented by this route must be an action of [[CaptchaAction]].
     * Please refer to [[\yii\helpers\Url::toRoute()]] for acceptable formats.
     */
    public $captchaAction = '/site/captcha';
    /**
     * @var array HTML attributes to be applied to the CAPTCHA image tag.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $imageOptions = [];
    /**
     * @var string the template for arranging the CAPTCHA image tag and the text input tag.
     * In this template, the token `{image}` will be replaced with the actual image tag,
     * while `{input}` will be replaced with the text input tag.
     */
    public $template = '{image} {input}';
    /**
     * @var array the HTML attributes for the input tag.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'form-control'];

    /**
     * Initializes the widget.
     */
    public function init(): void
    {
        parent::init();

        if (!isset($this->imageOptions['id'])) {
            $this->imageOptions['id'] = $this->options['id'] . '-image';
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $this->registerClientScript();
        $input = $this->renderInputHtml('text');
        $route = $this->captchaAction;
        if (is_array($route)) {
            $route['v'] = uniqid('', true);
        } else {
            $route = [$route, 'v' => uniqid('', true)];
        }
        $image = Html::img($route, $this->imageOptions);
        return strtr($this->template, [
            '{input}' => $input,
            '{image}' => $image,
        ]);
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $options = $this->getClientOptions();
        $options = empty($options) ? '' : Json::htmlEncode($options);
        $id = $this->imageOptions['id'];
        $view = $this->getView();
        CaptchaAsset::register($view);
        $view->registerJs("(new YiiCaptcha(document.getElementById('$id'))).init($options);");
    }

    /**
     * Returns the options for the captcha JS widget.
     *
     * @return array the options
     */
    protected function getClientOptions(): array
    {
        $route = $this->captchaAction;
        if (is_array($route)) {
            $route[CaptchaAction::REFRESH_GET_VAR] = 1;
        } else {
            $route = [$route, CaptchaAction::REFRESH_GET_VAR => 1];
        }

        return [
            'refreshUrl' => Url::toRoute($route),
            'hashKey' => 'yii-captcha-' . str_replace(trim($route[0], '/'), '/', '-'),
        ];
    }
}
