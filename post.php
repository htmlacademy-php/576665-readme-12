<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
    exit();
}

if (isset($_GET['post_id'])) {
    $param_id = filter_input(INPUT_GET, 'post_id');
    $post = '';

    $sql = 'SELECT * , users.id, post_types.id FROM posts'
        . ' JOIN users ON posts.user_id = users.id'
        . ' JOIN post_types ON posts.post_type_id = post_types.id'
        . ' WHERE post_id =' . ' ?';

    $stmt = db_get_prepare_stmt($link, $sql, [
        'i' => $param_id
    ]);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit ('error' . mysqli_error($link));
    }

    $post = mysqli_fetch_assoc($result);
}

if (empty($post)) {
    header("HTTP/1.0 404 Not Found");
    print ('PAGE NOT FOUND: ' . mysqli_error($link));
}

$sql = 'SELECT * FROM subscriptions'
    . ' WHERE subscriptions.author_id = ' . $post['user_id'];

$result = mysqli_query($link, $sql);

if (!$result) {
    exit ('error' . mysqli_error($link));
}

$subscriptions_count = mysqli_num_rows($result);

$sql = 'SELECT * FROM posts'
    . ' WHERE posts.user_id = ' . $post['user_id'];

$result = mysqli_query($link, $sql);

if (!$result) {
    exit ('error' . mysqli_error($link));
}

$posts_count = mysqli_num_rows($result);

$post_content = include_template("post/post-{$post['class']}.php", [
    'post' => $post
]);

$page_content = include_template('post.php', [
    'post' => $post,
    'post_content' => $post_content,
    'subscriptions_count' => $subscriptions_count,
    'posts_count' => $posts_count
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: публикация'
]);

print ($layout_content);
