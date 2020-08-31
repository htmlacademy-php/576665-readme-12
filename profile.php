<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

check_page_access();

$current_user = $_SESSION['user'];

$profile_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

$current_tab = isset($_GET['tab']) ? filter_input(INPUT_GET, 'tab', FILTER_DEFAULT) : 'posts';

$profile_data = get_user_data($link, (int)$profile_id);

if (empty($profile_data)) {
    header("HTTP/1.0 404 Not Found");
    exit ('PAGE NOT FOUND');
};

$posts = get_posts_by_parameter($link, 'user_id', $profile_data['id']);
var_dump($posts);
$profile_followers = get_followers($link, $profile_id);

$current_user_followers = get_followers($link, $current_user['id']);

if (!empty($profile_followers)) {
    foreach ($profile_followers as $key => $follower) {
        $profile_followers[$key]['is_following'] = in_array($follower['id'], array_column($current_user_followers, 'id')) ? 'true' : 'false';
        $profile_followers[$key]['is_current_user'] = $current_user['id'] === $follower['id'] ? 'true' : 'false';
    }
}

$profile_data['is_following'] = in_array($profile_id, array_column($current_user_followers, 'id')) ? 'true' : 'false';
$profile_data['is_current_user'] = $current_user['id'] === $profile_id ? 'true' : 'false';

if (!empty($posts)) {
    $posts_id = array_column($posts, 'post_id');
    $posts_tags = get_posts_tags($link, $posts_id);
    foreach ($posts as $key => $post) {
        $posts[$key]['tags'] = $posts_tags[$posts[$key]['post_id']] ?? '';
    }
    $posts_likes = get_posts_likes($link, $posts_id);
}

$tabs_content = include_template("profile/{$current_tab}.php", [
    'posts' => $posts ?? '',
    'posts_tags' => $posts_tags ?? '',
    'followers' => $profile_followers ?? '',
    'posts_likes' => $posts_likes
]);

$page_content = include_template('profile.php', [
    'current_tab' => $current_tab,
    'profile_data' => $profile_data,
    'tabs_content' => $tabs_content
]);

$layout = include_template('layout.php', [
    'content' => $page_content,
    'title' =>  'readme: мой профиль'
]);

print $layout;
