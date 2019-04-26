<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Yii\Captcha;

use yii\base\Action;
use yii\helpers\Url;
use yii\web\Response;

/**
 * CaptchaAction renders a CAPTCHA image.
 *
 * CaptchaAction is used together with [[Captcha]] and [[\Yiisoft\Yii\Captcha\CaptchaValidator]]
 * to provide the [CAPTCHA](http://en.wikipedia.org/wiki/Captcha) feature.
 *
 * You should configure [[driver]] with the actual CAPTCHA rendering driver to be used.
 * Note that different drivers may require different libraries or PHP extension installed.
 * Please refer to the particular driver class for details.
 *
 * Using CAPTCHA involves the following steps:
 *
 * 1. Override [[\yii\web\Controller::actions()]] and register an action of class CaptchaAction with ID 'captcha'
 * 2. In the form model, declare an attribute to store user-entered verification code, and declare the attribute
 *    to be validated by the 'captcha' validator.
 * 3. In the controller view, insert a [[Captcha]] widget in the form.
 *
 * @property string $verifyCode The verification code. This property is read-only.
 */
class CaptchaAction extends Action
{
    /**
     * The name of the GET parameter indicating whether the CAPTCHA image should be regenerated.
     */
    public const REFRESH_GET_VAR = 'refresh';

    /**
     * @var int how many times should the same CAPTCHA be displayed. Defaults to 3.
     * A value less than or equal to 0 means the test is unlimited.
     */
    public $testLimit = 3;
    /**
     * @var string the fixed verification code. When this property is set,
     * [[getVerifyCode()]] will always return the value of this property.
     * This is mainly used in automated tests where we want to be able to reproduce
     * the same verification code each time we run the tests.
     * If not set, it means the verification code will be randomly generated.
     */
    public $fixedVerifyCode;
    /**
     * @var DriverInterface|array|string the driver to be used for CAPTCHA rendering. It could be either an instance
     * of [[DriverInterface]] or its DI compatible configuration.
     * For example:
     *
     * ```php
     * [
     *     '__class' => \Yiisoft\Yii\Captcha\ImagickDriver::class,
     *     // 'backColor' => 0xFFFFFF,
     *     // 'foreColor' => 0x2040A0,
     * ]
     * ```
     *
     * After the action object is created, if you want to change this property, you should assign it
     * with a [[DriverInterface]] object only.
     */
    public $driver;

    /**
     * Runs the action.
     */
    public function run()
    {
        if ($this->driver === null || (is_array($this->driver) && !isset($this->driver['__class']))) {
            $this->driver['__class'] = GdDriver::class;
        }

        $this->driver = $this->app->ensureObject($this->driver, DriverInterface::class);

        if ($this->app->request->getQueryParam(self::REFRESH_GET_VAR) !== null) {
            // AJAX request for regenerating code
            $code = $this->getVerifyCode(true);
            $this->app->response->format = Response::FORMAT_JSON;
            return [
                'hash1' => $this->generateValidationHash($code),
                'hash2' => $this->generateValidationHash(strtolower($code)),
                // we add a random 'v' parameter so that FireFox can refresh the image
                // when src attribute of image tag is changed
                'url' => Url::to([$this->id, 'v' => uniqid()]),
            ];
        }

        $this->setHttpHeaders();
        $this->app->response->format = Response::FORMAT_RAW;

        return $this->driver->renderImage($this->getVerifyCode());
    }

    /**
     * Generates a hash code that can be used for client-side validation.
     * @param string $code the CAPTCHA code
     * @return string a hash code generated from the CAPTCHA code
     */
    public function generateValidationHash(string $code): string
    {
        for ($h = 0, $i = strlen($code) - 1; $i >= 0; --$i) {
            $h += ord($code[$i]);
        }

        return $h;
    }

    /**
     * Gets the verification code.
     * @param bool $regenerate whether the verification code should be regenerated.
     * @return string the verification code.
     */
    public function getVerifyCode(bool $regenerate = false): string
    {
        if ($this->fixedVerifyCode !== null) {
            return $this->fixedVerifyCode;
        }

        $session = $this->app->getSession();
        $session->open();
        $name = $this->getSessionKey();
        if ($regenerate || $session->get($name) === null) {
            $session->set($name, $this->driver->generateVerifyCode());
            $session->set($name . 'count', 1);
        }

        return $session->get($name);
    }

    /**
     * Validates the input to see if it matches the generated code.
     * @param string $input user input
     * @param bool $caseSensitive whether the comparison should be case-sensitive
     * @return bool whether the input is valid
     */
    public function validate(string $input, bool $caseSensitive): bool
    {
        $code = $this->getVerifyCode();
        $valid = $caseSensitive ? ($input === $code) : strcasecmp($input, $code) === 0;
        $session = $this->getApp()->getSession();
        $session->open();
        $name = $this->getSessionKey() . 'count';
        $session[$name] += 1;
        if ($valid || $session[$name] > $this->testLimit && $this->testLimit > 0) {
            $this->getVerifyCode(true);
        }

        return $valid;
    }

    /**
     * Returns the session variable name used to store verification code.
     * @return string the session variable name
     */
    protected function getSessionKey(): string
    {
        return '__captcha/' . $this->getUniqueId();
    }

    /**
     * Sets the HTTP headers needed by image response.
     */
    protected function setHttpHeaders(): void
    {
        $response = $this->getApp()->getResponse();
        $response->setHeader('Pragma', 'public');
        $response->setHeader('Expires', '0');
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $response->setHeader('Content-Transfer-Encoding', 'binary');
        $response->setHeader('Content-type', $this->driver->getImageMimeType());
    }
}
