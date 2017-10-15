<?php

require __DIR__ . '/vendor/autoload.php';

$packaglist = new \Godruoyi\Packagist\Packagist(
    'M58222SH95', 
    '5ae4d03c98685bd7364c2e0fd819af05'
);

$packaglist->except([
    'composer/installers', 
    'illuminate/*'
])->orderBy('downloads' => 'desc')
->resultPath(__DIR__ . '/test.md')
->search('laravel', 120);