<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

check_page_access();

$current_user_id = $_SESSION['user']['id'];

if (isset($_GET['post_id'])) {
    $post_id = filter_input(INPUT_GET, 'post_id');
    $post = get_posts_by_parameters($link, [
        'post_id' => $post_id
    ]);
    $post = call_user_func_array('array_merge', $post);

    if (empty($post)) {
        header("HTTP/1.0 404 Not Found");
        print ('PAGE NOT FOUND: ' . mysqli_error($link));
    }
    $author_data = get_user_data($link, $post['user_id']);
    $author_data['is_following'] = is_following($link, $current_user_id, $author_data['id']);
}

$post_content = include_template("post/post-{$post['class']}.php", [
    'post' => $post
]);

$page_content = include_template('post.php', [
    'post' => $post,
    'post_content' => $post_content,
    'author_data' => $author_data
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: публикация'
]);

print ($layout_content);
