<?php

$table = 'asd';

include_once __DIR__ . '/bootstrap.php';

$database = new \Deimos\Database\Database($db);

//foreach (range(1, 5) as $name)
//{
//    $query = $database->insert()
//        ->from($table)
//        ->value('name', mt_rand(10000, 99999));
//
//    var_dump($query->insert());
//}
//
//$query = $database->delete()->from($table)->where('id', '<', 400)->where('id', '>', 200);
//
//var_dump($query->delete());

$query = $database->update()->from($table)->where('id', '<', 200)->set('name', $database->raw('name + 5000'));

var_dump($query->update());
