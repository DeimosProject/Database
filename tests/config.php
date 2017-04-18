<?php

return [
    'mysql' => [
        'adapter'  => 'mysql',
        //    'host'     => 'localhost', // optional
        //    'port'     => 3306, // optional
        'database' => 'test',
        'username' => 'root',
        'password' => 'root',

        'options' => [
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],

    'pgsql' => [
        'adapter'  => 'pgsql',
        'database' => 'test',
        'username' => 'root',
        'password' => 'root',
    ],

    'default' => [
        'adapter' => 'sqlite',
        'path'    => ':memory:'
    ]
];
