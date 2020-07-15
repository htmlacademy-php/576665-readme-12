<?php

require_once 'init.php';
require_once 'helpers.php';

if (!$link) {
    print 'error' . mysqli_connect_error();
} else {
    $sql = 'SELECT * from post_types';
    $result = mysqli_query($link, $sql);
    if ($result) {
        $post_types = mysqli_fetch_all($result, MYSQLI_ASSOC);

    }
}

$active_post_type = filter_input (INPUT_GET, 'post_type', FILTER_VALIDATE_INT);
$array_index = ($active_post_type - 1);
var_dump($active_post_type);
var_dump($array_index);
echo '<br>массив $post_types <pre>';
var_dump( $post_types);
echo '</pre>';
var_dump($post_types[$array_index]['class']);


if ($_SERVER['REQUEST_METHOD' == 'POST']) {
    $new_post = $_POST;

}

$adding_post_content = include_template("adding-post-{$post_types[$array_index]['class']}.php", [
    'post_types' => $post_types,
    'array_index' => $array_index
]);


$page_content = include_template('adding-post.php', [
    'post_types' => $post_types,
    'active_post_type' => $active_post_type,
    'adding_post_content' => $adding_post_content,
    'array_index' => $array_index

]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: добавление публикации'

]);

print ($layout_content);
