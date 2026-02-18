<?php

declare(strict_types=1);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require dirname(__DIR__) . '/vendor/autoload.php';

require dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php';

// Override DB connection for tests if TEST_DB_* env vars are provided.
$dbConfig = require dirname(__DIR__) . '/config/db.php';

$config = [
    'id' => 'loan-api-test',
    'basePath' => dirname(__DIR__),
    'runtimePath' => dirname(__DIR__) . '/runtime',
    'components' => [
        'db' => $dbConfig,
        'request' => [
            'enableCsrfValidation' => false,
            'scriptUrl' => '/index.php',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require dirname(__DIR__) . '/config/routes.php',
        ],
        'mutex' => [
            'class' => 'yii\mutex\PgsqlMutex',
        ],
    ],
];

new yii\web\Application($config);
