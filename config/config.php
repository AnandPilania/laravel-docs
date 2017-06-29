<?php

return [
    'disk' => [
        'driver' => 'local',
        'root' => storage_path('app/docs')
    ],

    /*
     * Route::group([...]
     */
    'http' => [
        'prefix' => 'docs',
        'middleware' => 'web',
        'namespace' => 'AP\Docs'
    ],

    'default' => [
        'vendor' => 'laravel',
        'version' => '5.4',
        'page' => 'installation',
        'index' => 'documentation',
        'extension' => 'md'
    ],

    'security' => [
        'enabled' => false,
        'file' => 'security.json'
    ],

    'extensions' => [
        'filter' => true,
        'guess' => false,
        'supported' => ['txt', 'html'],
        'exclude' => ['json', 'git', 'php']
    ]
];