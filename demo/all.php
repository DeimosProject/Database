<?php

include_once __DIR__ . '/bootstrap.php';

$database = new \Deimos\Database\Database($db);

$userQuery = $database->query()
    ->from('users')
    ->limit(10);

$user = $userQuery->findOne();

var_dump($user);

$userQuery = $database->query('pgsql')
    ->from('users')
    ->where('id', '>', 15)
    ->limit(10);

$user = $userQuery->findOne();

var_dump($user);
