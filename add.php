<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

if (!$link) {
    print 'error' . mysqli_connect_error();
} else {
    $sql = 'SELECT * from post_types';
    $result = mysqli_query($link, $sql);
    if ($result) {
        $post_types_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

$post_types=[];
foreach ($post_types_array as $item) {
    $post_types[$item['id']] = $item;
}

$error_titles = [
    'title' => 'Заголовок',
    'content' => $post_type['class'] == 'text' ? 'Текст поста' : 'Текст цитаты',
    'author_quote' => 'Автор',
    'img' => 'Ссылка из интернета',
    'video' => 'Ссылка на YOUTUBE',
    'link' => 'Ссылка',
    ];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $active_post_type = filter_input (INPUT_GET, 'post_type', FILTER_VALIDATE_INT);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

    $new_post = filter_input_array(INPUT_POST, [
        'title' => FILTER_DEFAULT,
        'content' => FILTER_DEFAULT,
        'author_quote' => FILTER_DEFAULT,
        'img' => FILTER_DEFAULT,
        'video' => FILTER_DEFAULT,
        'link' => FILTER_DEFAULT,
        'post_type_id' => FILTER_DEFAULT,
        'tags' => FILTER_DEFAULT
    ], true);

    $active_post_type = $new_post['post_type_id'];
    $new_post['post_type'] = $post_types[$active_post_type]['class'];
    $new_post['user_id'] = 1;
    $new_post['view_count'] = 0;
    $new_post['tag_id'] = 1;

    if (!empty($new_post['tags'])) {
        $tags = explode(' ',$new_post['tags']);
        $sql = 'SELECT tag from tags';
        $result = mysqli_query($link, $sql);
        if ($result) {
            $tags_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        foreach ($tags as $tag) {
            if (is_new_tag($tag, $tags_array)) {
                print_r ($tag);
            }
        }
    }

    if ($new_post['post_type'] == 'photo') {
        if (empty($new_post['img']) and empty($_FILES['upload_photo']['name'])) {
            $errors['photo_post'] = 'Загрузите файл или заполните поле "ссылка из интернета"';
        } elseif (!empty($_FILES['upload_photo'])) {
            $new_post['photo'] = $_FILES['upload_photo'];
        }
    }

    $rules = rules($new_post['post_type'], $new_post['tags']);

    foreach ($new_post as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }

    $errors = array_filter($errors);

    if (!count($errors)) {
        if ($new_post['post_type'] == 'photo' and !empty($new_post['photo']['name'])) {
                $new_post['img'] = upload_photo($new_post['photo']);
            } elseif ($new_post['post_type'] == 'photo' and !empty($new_post['img'])) {
                $date = file_get_contents($new_post['img']);
                $headers = get_headers($new_post['img'], 1);
                $type = $headers['Content-Type'];
                $extension = substr($type, strpos($type, '/') + 1);
                $path = 'uploads/' . uniqid() . '.' . $extension;
                $new_post['img'] = $path;
                file_put_contents($path, $data);
            }

        $sql = 'INSERT INTO posts (date, title, content, author_quote, img, video, link, view_count, user_id, post_type_id, tag_id)
    VALUE (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [
            $new_post['title'],
            $new_post ['content'],
            $new_post['author_quote'],
            $new_post['img'],
            $new_post['video'],
            $new_post['link'],
            $new_post['view_count'],
            $new_post['user_id'],
            $new_post['post_type_id'],
            $new_post['tag_id']
        ]);

        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $post_id = mysqli_insert_id($link);
            header('Location: /post.php?post_id=' . $post_id);
        }
    }
}

$adding_post_content = include_template("adding-post-{$post_types[$active_post_type]['class']}.php", [
    'post_types' => $post_types,
    'active_post_type' => $active_post_type,
    'active_post_type_id' => $active_post_type_id,
    'new_post' => $new_post,
    'errors' => $errors
]);

$page_content = include_template('adding-post.php', [
    'post_types' => $post_types,
    'active_post_type' => $active_post_type,
    'active_post_type_id' => $active_post_type_id,
    'adding_post_content' => $adding_post_content,
    'new_post' => $new_post,
    'errors' => $errors,
    'errors_title' => $errors_title

]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: добавление публикации'

]);

print ($layout_content);
