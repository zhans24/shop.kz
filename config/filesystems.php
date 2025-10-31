<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */
    'default' => env('FILESYSTEM_DISK', 'public_uploads'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    */
    'disks' => [

        // системный локальный (НЕ public)
        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
            'throw'  => false,
            'report' => false,
        ],

        // наш публичный диск: сразу пишет в /public/uploads и отдает по /uploads
        'public_uploads' => [
            'driver'     => 'local',
            'root'       => public_path('uploads'),
            'url'        => '/uploads',
            'visibility' => 'public',
            'throw'      => false,
            'report'     => false,
        ],

        // (не обязателен, можно оставить для совместимости, но мы его не используем)
        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => rtrim(env('APP_URL'), '/').'/storage',
            'visibility' => 'public',
            'throw'      => false,
            'report'     => false,
        ],

        's3' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION'),
            'bucket'                  => env('AWS_BUCKET'),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw'                   => false,
            'report'                  => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    | нам не нужен симлинк — оставляем пусто (или как было, но не используем)
    */
    'links' => [
        // public_path('storage') => storage_path('app/public'),
    ],
];
