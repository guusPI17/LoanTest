<?php

declare(strict_types=1);

return [
    'id' => 'loan-api',
    'name' => 'Loan API',
    'basePath' => dirname(__DIR__),
    'runtimePath' => dirname(__DIR__) . '/runtime',
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'request' => [
            'enableCsrfValidation' => false,
            'scriptUrl' => '/index.php',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/routes.php',
        ],
        'mutex' => [
            'class' => 'yii\mutex\PgsqlMutex',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
];
