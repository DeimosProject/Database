<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$builder = new \Deimos\Builder\Builder();

$configObject = new \Deimos\Config\ConfigObject($builder, [
    'adapter'  => 'pgsql',
    'database' => 'test',
    'username' => 'root',
    'password' => 'root',
]);

$database = new \Deimos\Database\Database($configObject);

$userQuery = $database->query()
    ->from('users')
    ->where('id', '>', 15)
    ->limit(10);

$user = $userQuery->findOne();

$userQuery->limit(50);

$userQuery->where('id', '<', 20);

$users = $userQuery->find();

var_dump($user, $users);

$max = $database->query()
    ->select(['id' => $database->raw('max(id)')])
    ->from('users');

$user = $database->query()
    ->from('users')
    ->where('id', $max);

var_dump($user->findOne());

var_dump(
    $database->query()
        ->from('users')
        ->orderBy($database->raw('RANDOM()'))
        ->limit(15)
        ->find()
);