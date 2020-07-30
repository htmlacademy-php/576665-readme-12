<?php

require_once 'init.php';
require_once 'helpers.php';

function esc (string $str)
{
    $text = null;
    $text = htmlspecialchars($str);
    return $text;
}

function clean(string $value) {
    $value = trim($value);
    $value = stripslashes($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value);

    return $value;
}

function check_text(string $value)
{
    $error = '';
    if (empty($value)) {
        $error = 'Это поле должно быть заполнено';
    }
    return $error;
}

function check_youtube_domain (string $value)
{
    $domain = parse_url($value, PHP_URL_HOST);
    if (strpos($domain, 'youtube.com') == false) {
        return 'Введите ссылку на видео из YOUTUBE';
    }
    return '';
}

function link_validate (string $link_value)
{
    $error = '';
    if (empty($link_value)) {
        $error = "Это поле должно быть заполнено";
    } elseif (!filter_var($link_value, FILTER_VALIDATE_URL)) {
        $error = "Значение не является ссылкой";
    }
    return $error;
}

function tags_validate (array $tags_value)
{
    $tags_error = [];
    $tags_array = explode(' ', $tags_value);

    foreach ($tags_array as $tag) {

        if (!preg_match('/^[a-zA-Zа-яА-Я0-9]+$/', $tag)) {
            $tags_error[] = $tag;
        }

       if (!empty($tags_error)) {
           $count_error_tags = count($tags_error);
           $tag_error = get_noun_plural_form($count_error_tags, 'Значение ', 'Значения ', 'Значения ') . implode(', ', $tags_error) . get_noun_plural_form($count_error_tags, ' не корректно', ' не корректны', ' не корректны');
       }

    }
    return $tag_error;
}

function youtube_url_validation (string $url_value)
{
    $error = '';
    if (link_validate($url_value)) {
        $error = link_validate($url_value);
    } elseif (check_youtube_url($url_value) !== true) {
        $error = check_youtube_url($url_value);
    } elseif (check_youtube_domain($url_value)) {
        $error = check_youtube_domain($url_value);
    }
    return $error;
}

function check_img_type (string $file_type)
{
    $required_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file_type, $required_types)) {
        return 'Загрузите картинку в одном из допустимых форматов: JPG PNG GIF';
    }
    return true;
}

function photo_validate (array $upload_photo)
{
    $error = '';
    $tmp_name = $upload_photo['tmp_name'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($file_info, $tmp_name);
    if (check_img_type($file_type) !== true) {
        $error = check_img_type($file_type);
    }
    return $error;
}

function photo_link_validate (string $photo_link)
{
    $error = '';
    if (!empty($photo_link)) {
        if (!filter_var($photo_link, FILTER_VALIDATE_URL)) {
            $error = "Значение не является ссылкой";
        } else {
            $get_headers = get_headers($photo_link, 1);
            if (!strpos($get_headers[0], '200')) {
                $error = "Страница не отвечает";
            } elseif (check_img_type($get_headers['Content-Type']) !== true) {
                $error = "Ссылка на недопустимый тип файла";
            }
        }
    }
    return $error;
}

function upload_photo (array $upload_photo)
{
    $tmp_name = $upload_photo['tmp_name'];
    $path = $upload_photo['name'];
    $filename = 'uploads/' . uniqid() . '.' . pathinfo($path, PATHINFO_EXTENSION);
    move_uploaded_file($tmp_name, $filename);
    return $filename;
}

function is_new_tag (string $tag, array $existing_tags)
{
    return !(in_array($tag, $existing_tags));
}

function rules($post_type, $tags)
{
    $rules = [];
    $rules['title'] = function ($value) {
        return check_text($value);
    };
    if (!empty($tags)) {
        $rules['tags'] = function ($value) {
            return tags_validate($value);
        };
    }
    switch ($post_type) {
        case 'text':
            $rules['content'] = function ($value) {
                return check_text($value);
            };
            break;
        case 'link':
            $rules['link'] = function ($value) {
                return link_validate($value);
            };
            break;
        case 'video':
            $rules['video'] = function ($value) {
                return youtube_url_validation($value);
            };
            break;
        case 'quote':
            $rules['content'] = function ($value) {
                return check_text($value);
            };
            $rules['author_quote'] = function ($value) {
                return check_text($value);
            };
            break;
        case 'photo' :
            if (!empty($_FILES['upload_photo']['name'])) {
                $rules['photo'] = function ($value) {
                    return photo_validate($value);
                };
            } else {
                $rules['img'] = function ($value) {
                    return photo_link_validate($value);
                };
            }
            break;
    }
    return $rules;
}

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
            }
            if ($new_post['post_type'] == 'photo' and !empty($new_post['img'])) {
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
