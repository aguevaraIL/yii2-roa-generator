<?php

$appDir = dirname(__DIR__);
$testDir = dirname($appDir);
$repositoryDir = dirname($testDir);

return [
    'basePath' => $appDir,
    'language' => 'en-US',
    'bootstrap' => ['gii'],
    'aliases' => [
        '@tests' => $testDir,
        '@roaresearch/yii2/gii' => "$repositoryDir/src",
    ],
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'authManager' => [
             'class' => yii\rbac\DbManager::class,
        ],
    ],
    'modules' => [
        'gii' => [
            'class' => yii\gii\Module::class,
            'generators' => [
                'migrate' => roaresearch\yii2\gii\migrate\Generator::class,
            ],
        ],
    ],
];
