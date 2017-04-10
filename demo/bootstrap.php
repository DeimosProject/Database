<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$builder = new Deimos\Builder\Builder();
$helper  = new \Deimos\Helper\Helper($builder);
$config  = new \Deimos\Config\Config($helper, __DIR__);

$db = $config->get('config');
