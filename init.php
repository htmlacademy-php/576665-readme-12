<?php
$config = require 'config.php';
$link = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database']);
mysqli_set_charset($link, 'utf8');
