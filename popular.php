<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

check_page_access();

$post_types = get_post_types($link);

$post_type = !empty($_GET['post_type']) ? $_GET['post_type'] : '';
$sorting = !empty($_GET['sorting']) ? $_GET['sorting'] : 'view_count';
$order = !empty($_GET['order']) ? $_GET['order'] : 'DESC';

$params = [
  'post_type_id' => $post_type
];

$popular_posts = get_posts_by_parameters($link, $params, $sorting, $order);

$page_content = include_template('popular.php', [
    'popular_posts' => $popular_posts,
    'post_types' => $post_types,
    'active_post_type' => $post_type,
    'sorting' => $sorting,
    'order' => $order
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: популярное'
]);

print ($layout_content);
