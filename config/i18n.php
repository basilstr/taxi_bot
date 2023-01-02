<?php

return [
    'translations' => [
        'app*' => [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => dirname(__DIR__) . '/messages',
            'sourceLanguage' => 'uk',
            'fileMap' => [
                'app' => 'app.php',
            ],
            'forceTranslation' => true,

        ],
        '*' => [
            'class' => 'yii\i18n\PhpMessageSource'
        ],
    ],
];