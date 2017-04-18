<?php

include_once __DIR__ . '/bootstrap.php';

$database = new \Deimos\Database\Database($db, 'sqlite');

$database->exec('
    CREATE TABLE IF NOT EXISTS users (
      id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
      name varchar(255) NOT NULL
    );
');

$names = ['Alex', 'Max', 'Ivan', 'Dima', 'Serg'];

foreach (range(1, 1000) as $index)
{
    $database->insert()
        ->from('users')
        ->value('name', $names[mt_rand() % count($names)])
        ->insert();
}

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