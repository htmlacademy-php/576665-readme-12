<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

check_page_access();

$current_user = $_SESSION['user'];

$messages = get_messages($link, $current_user['id']);

$message_to = filter_input(INPUT_GET, 'message_to_id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_message = filter_input_array(INPUT_POST, [
        'message_content' => FILTER_DEFAULT
    ], true);
}

$page_content = include_template('messages.php', [
    'current_user' => $current_user
]);

$layout = include_template('layout.php', [
    'current_user' => $current_user,
    'content' => $page_content,
    'title' =>  'readme: мой профиль'
]);

print $layout;
