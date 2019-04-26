Basic Usage
===========

## [[Yiisoft\Yii\Captcha\CaptchaValidator]] <span id="captcha-validator"></span>

```php
[
    ['verificationCode', \Yiisoft\Yii\Captcha\CaptchaValidator::class],
]
```

Notes:

This validator is usually used together with [[Yiisoft\Yii\Captcha\CaptchaAction]] and [[Yiisoft\Yii\Captcha\Captcha]]
to make sure an input is the same as the verification code displayed by [[Yiisoft\Yii\Captcha\Captcha|CAPTCHA]] widget.

- `caseSensitive`: whether the comparison of the verification code is case sensitive. Defaults to `false`.
- `captchaAction`: the [route](structure-controllers.md#routes) corresponding to the
  [[Yiisoft\Yii\Captcha\CaptchaAction|CAPTCHA action]] that renders the CAPTCHA image. Defaults to `'site/captcha'`.
- `skipOnEmpty`: whether the validation can be skipped if the input is empty. Defaults to `false`,
  which means the input is required.