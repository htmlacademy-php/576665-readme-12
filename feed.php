<?php

require_once ('init.php');
require_once ('helpers.php');
require_once ('functions.php');

check_page_access();

$page_content = include_template('feed.php', [

]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: моя лента'
]);
print ($layout_content);
