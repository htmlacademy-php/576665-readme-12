<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

check_page_access();

$sql = 'SELECT id, name, class FROM post_types';
$result = mysqli_query($link, $sql);
if (!$result) {
    exit ('error '.mysqli_error($link));
}

$post_types = mysqli_fetch_all($result, MYSQLI_ASSOC);

$param_type = '';

$param_sort = 'view_count';

$query_type = filter_input(INPUT_GET, 'post_type');

if ($query_type) {
    $param_type = $query_type;
}

$sql = 'SELECT * , users.id, post_types.id FROM posts'
    . ' JOIN users ON posts.user_id = users.id'
    . ' JOIN post_types ON posts.post_type_id = post_types.id';

if ($param_type) {
    $sql .= " WHERE posts.post_type_id =" . $param_type;
}

if ($param_sort) {
    $sql .= " ORDER BY " . $param_sort . " DESC LIMIT 6 ";
}

$result = mysqli_query($link, $sql);

if (!$result) {
    exit ('error' . mysqli_error($link));
}

$popular_posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

$page_content = include_template('popular.php', [
    'popular_posts' => $popular_posts,
    'post_types' => $post_types,
    'param_type' => $param_type,
    'param_sort' => $param_sort
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: популярное'
]);

print ($layout_content);
