<?php

require_once 'init.php';
require_once 'helpers.php';

function esc ($str)
{
    $text = null;
    $text = htmlspecialchars($str);
    return $text;
}


function clean($value = "") {
    $value = trim($value);
    $value = stripslashes($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value);

    return $value;
}

//$test = ' ';
//echo 'test="' .  $test . '"';
//echo "<br>";
//$test_clean = clean($test);
//echo 'test_clean="' . $test_esc . '"';
//TODO esc() dont't work?

function link_validate ($link_value)
{
    if (!filter_var($link_value, FILTER_VALIDATE_URL)) {
        return "Значение не является ссылкой";
    }
    return true;
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
    return ($tag_error);
}

function youtube_url_validation ($url_value)
{
    if (link_validate($url_value)) {
        return link_validate($url_value);
    } elseif (check_youtube_url($url_value) !== true) {
        return check_youtube_url($url_value);
    }
    return true;
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

function post_validate ($new_post)
{
    $errors = [];
    $text_heading = clean($new_post['text_heading']);
    if(empty($text_heading)) {
        $errors['text_heading'] = 'Это поле должно быть заполнено';
    }
    if(!empty($new_post['tags'])) {
        $errors['tags'] = tags_validate($new_post['tags']);
    }
    if ($new_post['post_type'] === 'text') {
        $post_text = clean($new_post['post_text']);
        if (empty($post_text)) {
            $errors['post_text'] = 'Это поле должно быть заполнено';
        }
    }
    if ($new_post['post_type'] === 'video') {
        $video_heading = $new_post['video-heading'];
        if (empty($video_heading)) {
            $errors['video_heading'] = 'Это поле должно быть заполнено';
        } elseif (youtube_url_validation($video_heading) !== true) {
            $errors['video_heading'] = youtube_url_validation($video_heading);
        }
    }
    if ($new_post['post_type'] === 'quote') {
        $quote = clean($new_post['quote_text']);
        if(empty($quote)) {
            $errors['quote_text'] = 'Это поле должно быть заполнено';
        }
        $quote_author = clean($new_post['quote_author']);
        if(empty($quote_author)) {
            $errors['quote_author'] = 'Это поле должно быть заполнено';
        }
    }
    if($new_post['post_type'] === 'link') {
        $post_link = clean($new_post['post_link']);
        if (empty($post_link)) {
            $errors['post_link'] = 'Это поле должно быть заполнено';
        } else {
            $errors['post_link'] = link_validate($new_post['link']);
        }
    }
    if ($new_post['post_type'] === 'photo') {
        if (empty($_FILES['upload_photo']['name']) and empty($new_post['photo_heading'])) {
            $errors['post_photo'] = 'Поле "ссылка" должно быть заполнено или выбрана фотография для загрузки.';
        }
        if (!empty($_FILES['upload_photo']['name'])) {
            $tmp_name = $_FILES['upload_photo']['tmp_name'];
            $path = $_FILES['upload_photo']['name'];
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($file_info, $tmp_name);
            $filename = uniqid() . '.' . $file_type;
            echo ($file_type);
            if (check_img_type($file_type) !== true) {
                $errors['file_type'] =  check_img_type($file_type);
            }
            else {
                move_uploaded_file($tmp_name, 'uploads/' . $path);
                $upload_photo['path'] = $filename;
            }
        }
        if (!empty($new_post['photo_heading'])) {
            $photo_url = $new_post['photo_heading'];
            if (link_validate($photo_url) !== true) {
                $errors['photo_heading'] = link_validate($photo_url);
            } else {
                $get_headers = get_headers($photo_url);
                echo '<pre>';
                var_dump($get_headers);
                echo '</pre>';
                if ($get_headers[0] !== 'HTTP/1.1 200 OK') {
                    $errors['photo_heading'] = "Страница не отвечает";
                }
            }


                $data = file_get_contents($photo_url);
                $filename = uniqid();
                $path = 'uploads/' . $filename;
                file_put_contents($path, $data);
//                print '<a href="/uploads/' . $filename . '">File downloaded!</a>';
            }
        }
    }
    echo '<pre>';
    print 'errors_';
    var_dump ($errors);
    echo '</pre>';


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
    echo '<pre>';
    var_dump ($_FILES);
    echo '</pre>';
    post_validate($new_post);
    header("Location: http://576665-readme-12/add.php/?post_type=" . $active_post_type);
}



echo '<pre>';
print 'post_';
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
