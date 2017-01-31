<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$table = 'asd';

$builder = new \Deimos\Builder\Builder();

$configObject = new \Deimos\Config\ConfigObject(
    $builder,
    __DIR__ . '/config.php'
);

$database = new \Deimos\Database\Database($configObject);

//foreach (range(1, 5) as $name)
//{
//    $query = $database->insert()
//        ->from($table)
//        ->value('name', mt_rand(10000, 99999));
//
//    var_dump($query->insert());
//}

//$query = $database->delete()->from($table)->where('id', '<', 400)->where('id', '>', 200);
//
//var_dump($query->delete());

$query = $database->update()->from($table)->where('id', '<', 200)->set('name', $database->raw('name + 5000'));

var_dump($query->update());