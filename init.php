<?php
$config = require 'config.php';
var_dump($config['db']);
$link = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database']);
mysqli_set_charset($link, 'utf8');
