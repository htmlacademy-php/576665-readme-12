<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';
$template_date = [];

$page_content = include_template('registration.php', [
    'template_date' => $template_date,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: регистрация',
    'user_name' => 'Nadiia',
    'is_auth' => rand(0, 1)

]);

print ($layout_content);

