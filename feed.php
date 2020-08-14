<?php

require_once ('init.php');
require_once ('helpers.php');
require_once ('functions.php');

if (!isset($_SESION['user'])) {
    header("Location: /index.php");
    exit();
}
