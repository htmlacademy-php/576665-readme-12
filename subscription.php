<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

$current_user_id = $_SESSION['user']['id'];

$author_id = filter_input(INPUT_GET, 'author_id', FILTER_VALIDATE_INT);

if (!is_user_exist($link, $author_id, 'id')) {
    header("HTTP/1.0 404 Not Found");
    exit ();
}
if (is_following($link, $current_user_id, $author_id)) {
    $sql = "DELETE FROM subscriptions WHERE subscriptions.author_id = ? AND subscriptions.follower_id = ?";
} else {
    $sql = "INSERT INTO subscriptions (author_id, follower_id) VALUES (?, ?)";
}
$stmt = db_get_prepare_stmt($link, $sql, [$author_id, $current_user_id]);
mysqli_stmt_execute($stmt);

header("Location: {$_SERVER['HTTP_REFERER']}");
exit();
