<?php

return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common.php',
    [
        'id' => 'yii2-rmdb-server-tests',
        'modules' => [
            'rmdb' => roaresearch\yii2\rmdb\Module::class,
        ],
        'components' => [
            'mailer' => [
                'useFileTransport' => true,
            ],
            'user' => ['identityClass' => app\models\User::class],
            'urlManager' => [
                'showScriptName' => true,
                'enablePrettyUrl' => true,
            ],
            'request' => [
                'cookieValidationKey' => 'test',
                'enableCsrfValidation' => false,
            ],
            'errorHandler' => [
                'class' => yii\web\ErrorHandler::class,
            ],
        ],
        'params' => [],
    ]
);
