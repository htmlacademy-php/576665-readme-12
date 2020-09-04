<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

check_page_access();

$post_types = get_post_types($link);
$popular_posts = [];

$post_type = !empty($_GET['post_type']) ? $_GET['post_type'] : '';
$sorting = !empty($_GET['sorting']) ? $_GET['sorting'] : 'view_count';
$order = !empty($_GET['order']) ? $_GET['order'] : 'DESC';

$params = [
  'post_type_id' => $post_type
];

$posts_counts = get_posts_count($link, $params);
$post_per_page = 9;
if ($posts_counts) {
    $current_page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
    $pages_count = ceil($posts_counts / $post_per_page);
    $prev_page = ($current_page-1) > 0 ? $current_page-1 : '';
    $next_page = ($current_page+1) < $pages_count ? $current_page+1 : '';
    $offset = ($current_page - 1) * $post_per_page;
    $limit= $post_per_page;
    $popular_posts = get_posts_by_parameters($link, $params, $limit, $sorting, $order,  $offset);
}

$page_content = include_template('popular.php', [
    'popular_posts' => $popular_posts,
    'post_types' => $post_types,
    'active_post_type' => $post_type,
    'sorting' => $sorting,
    'order' => $order,
    'pages_count' => $pages_count ?? '',
    'prev_page' => $prev_page ?? '',
    'next_page' => $next_page ?? ''
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: популярное'
]);

print ($layout_content);

