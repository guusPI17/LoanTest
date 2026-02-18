<?php

declare(strict_types=1);

return [
    'id' => 'loan-api-console',
    'name' => 'Loan API Console',
    'basePath' => dirname(__DIR__),
    'runtimePath' => dirname(__DIR__) . '/runtime',
    'controllerNamespace' => 'yii\console\controllers',
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationNamespaces' => ['app\migrations'],
            'migrationPath' => null,
        ],
    ],
    'components' => [
        'db' => require __DIR__ . '/db.php',
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
