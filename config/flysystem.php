<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Flysystem.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Flysystem Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Examples of
    | configuring each supported driver is shown below. You can of course have
    | multiple connections per driver.
    |
    */

    'connections' => [

        'local' => [
            'driver'     => 'local',
            'path'       => storage_path('files'),
            // 'visibility' => 'public',
            // 'pirate'     => false,
            // 'eventable'  => true,
            // 'cache'      => 'foo'
        ],

        'null' => [
            'driver'    => 'null',
            // 'eventable' => true,
            // 'cache'     => 'foo'
        ],

        'sftp' => [
            'driver'     => 'sftp',
            'host'       => '118.126.117.251',
            'username'   => 'root',
            'privateKey' => storage_path('authorized_keys'),
            'root'       => '/data/',
            // 'port'       => 22,
            // 'password'   => 'pwd',
            // 'timeout'    => 20,
            // 'visibility' => 'public',
            // 'pirate'     => false,
            // 'eventable'  => true,
            // 'cache'      => 'foo'
        ],

        'webdav' => [
            'driver'     => 'webdav',
            'baseUri'    => 'http://example.org/dav/',
            'userName'   => 'your-username',
            'password'   => 'your-password',
            // 'visibility' => 'public',
            // 'pirate'     => false,
            // 'eventable'  => true,
            // 'cache'      => 'foo'
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Flysystem Cache
    |--------------------------------------------------------------------------
    |
    | Here are each of the cache configurations setup for your application.
    | There are currently two drivers: illuminate and adapter. Examples of
    | configuration are included. You can of course have multiple connections
    | per driver as shown.
    |
    */

    'cache' => [

        'foo' => [
            'driver'    => 'illuminate',
            'connector' => null, // null means use default driver
            'key'       => 'foo',
            // 'ttl'       => 300
        ],

        'bar' => [
            'driver'    => 'illuminate',
            'connector' => 'redis', // config/cache.php
            'key'       => 'bar',
            'ttl'       => 600,
        ],

        'adapter' => [
            'driver'  => 'adapter',
            'adapter' => 'local', // as defined in connections
            'file'    => 'flysystem.json',
            'ttl'     => 600,
        ],
    ],

];
