<?php
return [
  'default' => 'file',
  'drivers' => [
    'apc' => [
        'prefix' => 'cache',
    ],
    'file' => [
        'prefix' => 'cache',
        'path' => path('storage/cache')
    ],
    'database' => [
        'prefix' => 'cache',
        'table' => 'cache',
        'columns' => [
            'key' => 'key',
            'payload' => 'value',
            'expired' => 'expiration'
        ],
    ],
    'memcache' => [
        'prefix' => 'cache',
        'host' => '127.0.0.1',
        'port' => 11211,
        'persistent' => false,
        'weight' => 100,
        'timeout' => 1,
        'retry_interval' => 15,
    ],
    'memcached' => [
        'prefix' => 'cache',
        'persistent_id' => null,
        'host' => '127.0.0.1',
        'port' => 11211,
        'weight' => 100,
        'sasl' => [
            // 'username',
            // 'password'
        ],
        'options' => [
            // Memcached::OPT_CONNECT_TIMEOUT  => 2000,
        ]
    ],
    'redis' => [
        'prefix' => 'cache',
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => null,
        'timeout' => 2.5,
        'password' => null,
        'persistent' => true,
        'retry_interval' => 0,
        'persistent_id' => null,
    ]
  ]
];