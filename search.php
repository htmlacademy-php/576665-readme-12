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

    if (substr($search_query, 0, 1) === '#') {
        $search_tag = substr($search_query, 1);
        $sql = "SELECT id FROM tags WHERE tag = ?";
        $stmt = db_get_prepare_stmt($link, $sql, [$search_tag]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            exit ('error' . mysqli_error($link));
        }
        $search_tags_id = mysqli_fetch_row($result);
        $search_tags_id = implode('', $search_tags_id);
        var_dump($search_tags_id);

        $sql = 'SELECT post_id FROM post_tag '
            . 'WHERE tag_id = ' . $search_tags_id;
        $result = mysqli_query($link, $sql);
        $post_id = mysqli_fetch_all($result, MYSQLI_ASSOC);

        var_dump($post_id);
        $post_id = implode(', ', array_column($post_id, 'post_id'));
        print $post_id;

        $sql = 'SELECT * FROM posts WHERE post_id = (' . $post_id . ')';
        print $sql;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            exit ('error' . mysqli_error($link));
        }
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    } else {

        $sql = 'SELECT * FROM posts '
            . 'JOIN users ON posts.user_id = users.id '
            . 'JOIN post_types ON posts.post_type_id = post_types.id '
            . 'WHERE MATCH(title, content) AGAINST(?) ';
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
    'posts' => $posts,
    'search_query' => $search_query
]);

$layout = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: результаты поиска'
]);

print $layout;
