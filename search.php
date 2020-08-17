<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: /index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search_query = $_GET['q'] ?? '';

    if ($search_query) {
        $sql = 'SELECT * FROM posts '
            . 'JOIN users ON posts.user_id = users.id '
            . 'WHERE MATCH(title, content) AGAINST(?)';
        $stmt = db_get_prepare_stmt($link, $sql, [$search_query]);
        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            exit('error' . mysqli_error($link));
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
        var_dump($posts);
    }


}



$page_content = include_template('search-results.php', [
    'search_query' => $search_query,

]);

$layout = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: результаты поиска'
]);

print $layout;
