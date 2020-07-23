<?php

require_once 'init.php';
require_once 'helpers.php';

function esc ($str)
{
    $text = null;
    $text = htmlspecialchars($str);
    return $text;
}


function clean($value) {
    $value = trim($value);
    $value = stripslashes($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value);

    return $value;
}

function check_text($value)
{
    $error = '';
    if (empty($value)) {
        $error = 'Это поле должно быть заполнено';
    }
    return $error;
}

//$test = ' ';
//echo 'test="' .  $test . '"';
//echo "<br>";
//$test_clean = clean($test);
//echo 'test_clean="' . $test_esc . '"';
//TODO esc() dont't work?

function link_validate ($link_value)
{
    $error = '';
    if (empty($link_value)) {
        $error = "Это поле должно быть заполнено";
    } elseif (!filter_var($link_value, FILTER_VALIDATE_URL)) {
        $error = "Значение не является ссылкой";
    }
    return $error;
}

function tags_validate ($tags_value)
{
    $tag_error = [];
    $tags_array = explode(' ', $tags_value);
    foreach ($tags_array as $tag) {
        if (!preg_match('/^[a-zA-Zа-яА-Я0-9]+$/', $tag)) {
            $tag_error[] = 'Введенный тег "' . $tag . '" не корректен';
        }
    }
    return $tag_error;
}

function youtube_url_validation ($url_value)
{
    $error = '';
    if (link_validate($url_value)) {
        $error = link_validate($url_value);
    } elseif (check_youtube_url($url_value) !== true) {
        $error = check_youtube_url($url_value);
    }
    return $error;
}
//TODO check youtube domain

function check_img_type ($file_type)
{
    $required_types = ['image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file_type, $required_types)) {
        return 'Загрузите картинку в одном из допустимых форматов: JPG PNG GIF';
    }
    return true;
}

function photo_validate ($upload_photo)
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

function photo_link_validate ($photo_link)
{
    $error = '';
    if (link_validate($photo_link) !== true) {
        $error = link_validate($photo_link);
    } else {
        $get_headers = get_headers($photo_link);
        if (!strpos($get_headers[0], '200')) {
            $error = "Страница не отвечает";
        }
        if (check_img_type($get_headers[2]) !== true) {
            $error = "Ссылка на недопустимый тип файла";
//                    TODO check image type
        }
    }
    return $error;
}

function rules ($post_type, $tags)
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
        case 'text': {
            $rules['content'] = function ($value) {
                return check_text($value);
            };
        }
        case 'link': {
            $rules['link'] = function ($value) {
                return link_validate($value);
            };
            break;
        }
        case 'video': {
            $rules['video'] = function ($value) {
                return youtube_url_validation($value);
            };
            break;
        }
        case 'quote': {
            $rules['content'] = function ($value) {
                return check_text($value);
            };
            $rules['author_quote'] = function ($value) {
                return check_text($value);
            };
            break;
        }
        case 'photo' : {
            if (!empty($_FILES['upload_photo'])) {
                $rules['photo'] = function ($value) {
                    return photo_validate($value);
                };} else {
                    $rules['img'] = function ($value) {
                        return photo_link_validate($value);
                    };
                }
            }
        }
    return $rules;
}

//function post_validate ($new_post)
//{
//    if ($new_post['post_type'] === 'photo') {
//        $photo_url = $new_post['photo_heading'];
//        $upload_photo = $_FILES['upload_photo'];
//        if (empty($upload_photo['name']) and empty($new_post['photo_heading'])) {
//            $errors['post_photo'] = 'Поле "ссылка" должно быть заполнено или выбрана фотография для загрузки.';
//        }
//        if (!empty($upload_photo['name'])) {
//            $tmp_name = $upload_photo['tmp_name'];
//            $name = $upload_photo['name'];
//            $file_info = finfo_open(FILEINFO_MIME_TYPE);
//            $file_type = finfo_file($file_info, $tmp_name);
//            if (check_img_type($file_type) !== true) {
//                $errors['file_type'] =  check_img_type($file_type);
//            }
//            else {
//                move_uploaded_file($tmp_name, 'uploads/' . $name);
//                $upload_photo['path'] = 'uploads/' . $name;
//            }
//        }
//        if (!empty($new_post['photo_heading'])) {
//
//            if (link_validate($photo_url) !== true) {
//                $errors['photo_heading'] = link_validate($photo_url);
//            } else {
//                $get_headers = get_headers($photo_url);
//                echo '<pre>';
//                var_dump($get_headers);
//                echo '</pre>';
//                if (!strpos($get_headers[0], '200')) {
//                    $errors['photo_heading'] = "Страница не отвечает";
//                }
//                if (check_img_type($get_headers[2]) !== true) {
//                    $errors['photo_heading'] = "Ссылка на недопустимый тип файла";
////                    TODO check image type
//                }
//            }
//                $data = file_get_contents($photo_url);
//                $filename = uniqid();
//                $path = 'uploads/' . $filename;
//                file_put_contents($path, $data);
////                print '<a href="/uploads/' . $filename . '">File downloaded!</a>';
//            }
//        }
//    }

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

$active_post_type = filter_input (INPUT_GET, 'post_type', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];

//    $new_post = $_POST;

    $new_post = filter_input_array(INPUT_POST, [
        'title' => FILTER_DEFAULT,
        'content' => FILTER_DEFAULT,
        'author_quote' => FILTER_DEFAULT,
        'img' => FILTER_DEFAULT,
        'video' => FILTER_DEFAULT,
        'link' => FILTER_DEFAULT,
        'user_id' => '1',
        'post_type' => FILTER_DEFAULT,
        'tag_id' => '1'
    ], true);

    $rules = rules($new_post['post_type'], $new_post['tags']);

    foreach ($new_post as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }

}

echo '<pre>';
print 'post_';
var_dump($new_post);
echo '</pre>';

echo '<pre>';
print 'rules_';
var_dump($rules);
echo '</pre>';

echo '<pre>';
print 'errors_';
var_dump($errors);
echo '</pre>';




    $sql = 'INSERT INTO posts (title, content, author_quote, img, video, link, user_id, post_type_id, tag_id)
    VALUE (?, ?, ?, ?, ?, ?, 1, 2, 1)';
    $stmt = db_get_prepare_stmt($link, $sql, $new_post);
    $res = mysqli_stmt_execute($stmt);


if ($res) {
    $post_id = mysqli_insert_id($link);
    header('Location: post.php?post_id=' . $post_id);
}

$adding_post_content = include_template("adding-post-{$post_types[$active_post_type]['class']}.php", [
    'post_types' => $post_types,
    'active_post_type' => $active_post_type
]);


$page_content = include_template('adding-post.php', [
    'post_types' => $post_types,
    'active_post_type' => $active_post_type,
    'adding_post_content' => $adding_post_content

]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: добавление публикации'

]);

print ($layout_content);
