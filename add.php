<?php

require_once 'init.php';
require_once 'helpers.php';

function link_validate ($link_value)
{
    $link_errors = [];
    if (empty($link_value)) {

        $link_errors[] = "Поле должно быть заполнено";
    }
    if (!filter_var($link_value, FILTER_VALIDATE_URL)) {
        $link_errors[] = "Значение не является ссылкой";
    }
    return ($link_errors);
}

function tags_validate ($tags)
{
    $tag_errors = [];
    $tags_array = explode(' ', $tags);
    $tags_count = count($tags_array);
    var_dump($tags_array);
}

function post_validate ($new_post)
{
    $errors = [];
    if(empty($new_post['text-heading'])) {
        $errors['text-heading'] = 'Это поле должно быть заполнено';
    }
    tags_validate($new_post['tags']);
    var_dump($errors);

}

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_post = $_POST;
    post_validate($new_post);

    $file_name = uniqid() . '.img';
    $new_post['path'] = $file_name;
    $path = 'uploads/' . $file_name;
    move_uploaded_file($_FILES['upload_photo']['tmp_name'], 'uploads/' . $file_name);
}



echo '<pre>';
var_dump($new_post);
var_dump($new_post['post_link']);
var_dump($_FILES);
echo '</pre>';

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
