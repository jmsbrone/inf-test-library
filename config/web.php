<?php

use yii\symfonymailer\Mailer;

$params = require __DIR__ . '/params.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'CZ2OFzJV6jHithfIe7McN0brq1oQKGae',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                /** @uses \app\controllers\AuthorsController::actionList() */
                'authors' => 'authors/list',
                /** @uses \app\controllers\AuthorsController::actionCreate() */
                'authors/new' => 'authors/create',
                /** @uses \app\controllers\AuthorsController::actionView() */
                'GET author/<id:\d+>' => 'authors/view',
                /** @uses \app\controllers\AuthorsController::actionUpdate() */
                'POST author/<id:\d+>' => 'authors/update',
                /** @uses \app\controllers\AuthorsController::actionDelete() */
                'POST author/<id:\d+>/delete' => 'authors/delete',

                /** @uses \app\controllers\BooksController::actionList() */
                'books' => 'books/list',
                /** @uses \app\controllers\BooksController::actionCreate() */
                'books/new' => 'books/create',
                /** @uses \app\controllers\BooksController::actionView() */
                'GET book/<id:\d+>' => 'books/view',
                /** @uses \app\controllers\BooksController::actionUpdate() */
                'POST book/<id:\d+>' => 'books/update',
                /** @uses \app\controllers\BooksController::actionDelete() */
                'POST book/<id:\d+>/delete' => 'books/delete',
            ],
        ],

        ...(require_once __DIR__ . '/common_components.php'),
    ],
    'params' => $params,
    'container' => [
        'definitions' => require __DIR__ . '/di.php',
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
