<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', \App\Core\Values\Enums\FilesystemDiskEnum::LOCAL),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [
        \App\Core\Values\Enums\FilesystemDiskEnum::LOCAL_IMAGES => [
            'driver' => 'local',
            'root' => storage_path('app/images'),
        ],

        \App\Core\Values\Enums\FilesystemDiskEnum::UPLOADS => [
            'driver' => 'local',
            'root' => storage_path('app/uploads'),
        ],

        \App\Core\Values\Enums\FilesystemDiskEnum::MEDIA => [
            'driver' => 'local',
            'root' => storage_path('app/media'),
        ],

        \App\Core\Values\Enums\FilesystemDiskEnum::LOCAL => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        \App\Core\Values\Enums\FilesystemDiskEnum::PUBLIC => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],
    ],
];
