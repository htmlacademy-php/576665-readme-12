<?php

require_once 'init.php';
require_once 'helpers.php';

if (isset($_GET['post_id'])) {
    $param_id = filter_input(INPUT_GET, 'post_id');
}

if (empty($link)) {
    print ('error' . mysqli_connect_error());
} else {
    $sql = 'SELECT * , users.id, post_types.id FROM posts'
        . ' JOIN users ON posts.user_id = users.id'
        . ' JOIN post_types ON posts.post_type_id = post_types.id'
        . ' WHERE post_id =' . $param_id;

    $result = mysqli_query($link, $sql);

    if ($result) {
        $post = mysqli_fetch_assoc($result);
    }

    $sql = 'SELECT * FROM subscriptions'
        . ' WHERE subscriptions.author_id = ' . $post['user_id'];

    $result = mysqli_query($link, $sql);

    if ($result) {
        $subscriptions_count = mysqli_num_rows($result);
    }

    $sql = 'SELECT * FROM posts'
        . ' WHERE posts.user_id = ' . $post['user_id'];

    $result = mysqli_query($link, $sql);

    if($result) {
        $posts_count = mysqli_num_rows($result);
    }
}

if (empty($post)) {
    header("HTTP/1.0 404 Not Found");
    $error_msg = 'Не удалось выполнить запрос: ' . mysqli_error($link);
    print ($error_msg);
}

$post_content = include_template("post-{$post['class']}.php", [
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
