<?php

require __DIR__ . '/vendor/autoload.php';


// $s = '{"requests":[{"indexName":"packagist","params":"facetFilters=%5B%5B%22tags%3Alaravel%22%5D%5D"}]}';

// $s = json_decode($s, true);

// var_dump($s);
// exit;

(new \Godruoyi\Packagist\Packagist('M58222SH95', '5ae4d03c98685bd7364c2e0fd819af05'))->search();