<?php

require_once 'init.php';
require_once 'helpers.php';

function esc ($str)
{
    $text = null;
    $text = htmlspecialchars($str);
    return $text;
}

function link_validate ($link_value)
{
    $link_error = null;
    if (!filter_var($link_value, FILTER_VALIDATE_URL)) {
        $link_error = "Значение не является ссылкой";
    }
    return ($link_error);
}

function tags_validate ($tags_value)
{
    $tag_error = [];
    $tags_array = explode(' ', $tags_value);
    var_dump($tags_array);
    foreach ($tags_array as $tag) {
        if (!preg_match('/^[a-zA-Zа-яА-Я0-9]+$/', $tag)) {
            $tag_error[] = 'Введенный тег "' . $tag . '" не корректен';
        }
    }
    return ($tag_error);
}


function post_validate ($new_post)
{
    $errors = [];
    $new_post['text_heading'] = htmlspecialchars($new_post['text_heading']);
    print ($new_post['text_heading']);
    if(empty($new_post['text_heading'])) {
        $errors['text_heading'] = 'Это поле должно быть заполнено';
    }
    if(!empty($new_post['tags'])) {
        $errors['tags'] = tags_validate($new_post['tags']);
    }
    if ($new_post['post_type'] === 'text') {
        if (empty($new_post['post_text'])) {
            $errors['post_text'] = 'Это поле должно быть заполнено';
        }
    }
    if($new_post['post_type'] === 'video') {
        if (empty($new_post['video_heading'])) {
            $errors['video_heading'] = 'Это поле должно быть заполнено';
        }
    }
    if ($new_post['post_type'] === 'quote') {
        if(empty($new_post['quote_text'])) {
            $errors['quote_text'] = 'Это поле должно быть заполнено';
        }
        if(empty($new_post['quote_author'])) {
            $errors['quote_author'] = 'Это поле должно быть заполнено';
        }
    }
    if($new_post['post_type'] === 'link') {
        if (empty($new_post['post_link'])) {
            $errors['post_link'] = 'Это поле должно быть заполнено';
        } else {
            $errors['post_link'] = link_validate($new_post['link']);
        }
    }
    echo '<pre>';
    var_dump($errors);
    echo '</pre>';
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
