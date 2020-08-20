<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $posts = [];

    $search_query = trim($_GET['q']) ?? '';
    print $search_query;

    if (substr($search_query, 0, 1) === '#') {
        $search_tag = substr($search_query, 1);
        $sql = "SELECT posts.*,
       users.login,
       users.picture,
       users.registered,
       post_types.class,
       GROUP_CONCAT(tags.tag)
FROM (SELECT posts.*
     FROM posts
         JOIN post_tag ON posts.post_id = post_tag.post_id
         JOIN tags ON post_tag.tag_id = tags.id
     WHERE tags.tag = ?) as posts
    JOIN post_tag ON posts.post_id = post_tag.post_id
    JOIN tags ON post_tag.tag_id = tags.id
    JOIN post_types ON posts.post_type_id = post_types.id
    JOIN users ON posts.user_id = users.id
GROUP BY posts.post_id;";
        $stmt = db_get_prepare_stmt($link, $sql, [$search_tag]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            exit ('error' . mysqli_error($link));
        }

        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    } else {
        $sql = "SELECT posts.*,
       users.login,
       users.picture,
       users.registered,
       post_types.class,
       GROUP_CONCAT(tags.tag)
FROM  posts
    JOIN post_tag ON posts.post_id = post_tag.post_id
    JOIN tags ON post_tag.tag_id = tags.id
    JOIN post_types ON posts.post_type_id = post_types.id
    JOIN users ON posts.user_id = users.id
WHERE MATCH(title, content) AGAINST(?)
GROUP BY posts.post_id;";

        $stmt = db_get_prepare_stmt($link, $sql, [$search_query]);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            exit ('error' . mysqli_error($link));
        }

        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    }

}

$page_content = include_template('search-results.php', [
    'posts' => $posts ?? '',
    'search_query' => $search_query
]);

$layout = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: результаты поиска'
]);

print $layout;
