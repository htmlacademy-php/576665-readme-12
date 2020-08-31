<?php

require_once ('init.php');
require_once ('helpers.php');
require_once ('functions.php');

check_page_access();

$current_user = $_SESSION['user']['id'];

$sql = "SELECT subscriptions.author_id FROM subscriptions WHERE follower_id = {$current_user}";
$result = mysqli_query($link, $sql);
if (!$result) {
    exit ('error' . mysqli_error($link));
}
$authors = mysqli_fetch_all($result, MYSQLI_ASSOC);

$authors_id_string = implode (', ' , array_column($authors, 'author_id'));

$posts = get_posts_by_parameter($link, 'user_id', $authors_id_string);


if (!empty($posts)) {
    $posts_id = array_column($posts, 'post_id');
    $posts_tags = get_posts_tags($link, $posts_id);

    foreach ($posts as $key => $post) {
        $posts[$key]['tags'] = $posts_tags[$posts[$key]['post_id']] ?? '';
    }
}
var_dump($posts);
$page_content = include_template('feed.php', [
    'posts' => $posts ?? ''

]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: моя лента'
]);
print ($layout_content);
