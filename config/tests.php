<?php

$_ENV['TEST_RUNTIME_PATH'] = $_ENV['TEST_RUNTIME_PATH'] ?? dirname(__DIR__) . '/runtime';

return [
    'app' => [
        'id' => 'testapp',
        'aliases' => [
            '@webroot'           => '@Yiisoft/Yii/Captcha/Tests/Data/web',
            '@runtime'           => $_ENV['TEST_RUNTIME_PATH'],
            '@yii/tests/runtime' => $_ENV['TEST_RUNTIME_PATH'],
        ],
    ],
    'assetManager' => [
        '__class'   => yii\web\AssetManager::class,
        'basePath'  => '@webroot/assets',
        'baseUrl'   => '@web/assets',
    ],
    'view' => [
        '__class' => \yii\web\View::class,
    ],
    'session' => [
        '__class' => \Yiisoft\Yii\Captcha\Tests\Data\Session::class,
    ],
];
