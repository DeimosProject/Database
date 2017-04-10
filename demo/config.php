<?php

return [
    'adapter'  => 'mysql',
    //    'host'     => 'localhost', // optional
    //    'port'     => 3306, // optional
    'database' => 'test',
    'username' => 'root',
    'password' => 'root',

    'options'  => [
//        \PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];