<?php

require_once ('init.php');
require_once ('helpers.php');
require_once ('functions.php');

check_page_access();
$post_types = get_post_types($link);

$active_post_type = isset($_GET['post_type']) ? filter_input(INPUT_GET, 'post_type') : '';

$current_user = $_SESSION['user']['id'];

$sql = "SELECT subscriptions.author_id FROM subscriptions WHERE follower_id = {$current_user}";
$result = mysqli_query($link, $sql);
if (!$result) {
    exit ('error' . mysqli_error($link));
}
$authors = mysqli_fetch_all($result, MYSQLI_ASSOC);

$authors_id_string = implode (', ' , array_column($authors, 'author_id'));

$params = [
    'user_id' => $authors_id_string,
    'post_type_id' => $active_post_type
];

$posts = get_posts_by_parameter($link, $params);

if (!empty($posts)) {
    $posts_id = array_column($posts, 'post_id');
    $posts_tags = get_posts_tags($link, $posts_id);

    foreach ($posts as $key => $post) {
        $posts[$key]['tags'] = $posts_tags[$posts[$key]['post_id']] ?? '';
    }
}

$page_content = include_template('feed.php', [
    'posts' => $posts ?? '',
    'active_post_type' => $active_post_type,
    'post_types' => $post_types

]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: моя лента'
]);
print ($layout_content);
