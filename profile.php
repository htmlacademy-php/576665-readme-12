<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

$current_user = $_SESSION['user'];

$profile_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

$current_tab = isset($_GET['tab']) ? filter_input(INPUT_GET, 'tab', FILTER_DEFAULT) : 'posts';

$profile_data = get_user_data($link, (int)$profile_id);

if (empty($profile_data)) {
    header("HTTP/1.0 404 Not Found");
    exit ('PAGE NOT FOUND');
};

$posts = get_posts_by_parameter($link, 'user_id', $profile_data['id']);

if (!empty($posts)) {
    $posts_id = array_column($posts, 'post_id');
    $posts_tags = get_posts_tags($link, $posts_id);
    foreach ($posts as $key => $post) {
        $posts[$key]['tags'] = $posts_tags[$posts[$key]['post_id']] ?? '';
    }
}

$tabs_content = include_template("profile/{$current_tab}.php", [
    'posts' => $posts ?? ''
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
