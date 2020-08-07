<?php

function esc(string $str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

function cut_text($text, $excerpt_length = 300)
{
    $text_length = mb_strlen($text);
    if ($text_length > $excerpt_length) {
        //cut string
        $text = mb_substr($text, 0, $excerpt_length);
        //cut to last space
        $text = mb_substr($text, 0, mb_strrpos($text, ' '));
        //add '...' and link to full post
        $text = '<p>' . $text . '...' . '</p>' . '<a class="post-text__more-link" href="#">Читать далее</a>';
    } else {
        $text = '<p>' . $text . '</p>';
    }
    return $text;
}

function relative_date($post_date)
{

    $publish_date = date_create($post_date);
    $cur_date = date_create('now');
    $diff = date_diff($cur_date, $publish_date);

    if ($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h == 0 && $diff->i == 0 && $diff->s > 0) {
        $diff_s = $diff->s;
        echo $diff_s . ' ' . get_noun_plural_form($diff_s, 'секунда', 'секунды', 'секунд') . ' ' . 'назад';
    } elseif ($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h == 0 && $diff->i > 0) {
        $diff_min = $diff->i;
        echo $diff_min . ' ' . get_noun_plural_form($diff_min, 'минута', 'минуты', 'минут') . ' ' . 'назад';
    } elseif ($diff->y == 0 && $diff->m == 0 && $diff->d == 0 && $diff->h > 0) {
        $diff_h = $diff->h;
        echo $diff_h . ' ' . get_noun_plural_form($diff_h, 'час', 'часа', 'часов') . ' ' . 'назад';
    } elseif ($diff->y == 0 && $diff->m == 0 && $diff->d < 7) {
        $diff_d = $diff->d;
        echo $diff_d . ' ' . get_noun_plural_form($diff_d, 'день', 'дня', 'дней') . ' ' . 'назад';
    } elseif ($diff->y == 0 && $diff->m == 0 && $diff->d >= 7 && $diff->d < 35) {
        $diff_weeks = $diff->d / 7;
        echo $diff_weeks . ' ' . get_noun_plural_form($diff_weeks, 'неделя', 'недели', 'недель') . ' ' . 'назад';
    } else {
        $diff_m = $diff->y * 12 + $diff->m;
        echo $diff_m . ' ' . get_noun_plural_form($diff_m, 'месяц', 'месяца', 'месяцев') . ' ' . 'назад';
    }
}

function get_active_post_type(array $array, string $id)
{
    foreach ($array as $key => $value) {
        if ($value['id'] === $id) {
            return $value['class'];
        }
    }
    return null;
}

function check_text(string $value)
{
    return empty($value) ? 'Это поле должно быть заполнено' : '';
}

function check_youtube_domain(string $value)
{
    $domain = parse_url($value, PHP_URL_HOST);
    return strpos($domain, 'youtube.com') === false ? 'Введите ссылку на видео из YOUTUBE' : '';
}

function link_validate(string $link_value)
{
    if (empty($link_value)) {
        return "Это поле должно быть заполнено";
    } elseif (!filter_var($link_value, FILTER_VALIDATE_URL)) {
        return "Значение не является ссылкой";
    }
}

function tags_validate(string $tags_value)
{
    $invalid_tags = [];
    $tag_error = '';
    $tags_array = explode(' ', $tags_value);
    if (!empty($tags_value)) {
        foreach ($tags_array as $tag) {

            if (!preg_match('/^[a-zA-Zа-яёА-ЯЁ0-9]+$/', $tag)) {
                $invalid_tags[] = $tag;
            }

            if (!empty($invalid_tags)) {
                $count_invalid_tags = count($invalid_tags);
                $tag_error = get_noun_plural_form($count_invalid_tags, 'Тег ', 'Теги ', 'Теги ') . implode(', ',
                        $invalid_tags) . get_noun_plural_form($count_invalid_tags, ' не корректен', ' не корректны',
                        ' не корректны');
            }

        }
    }
    return $tag_error;
}

function youtube_url_validation(string $url_value)
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

function check_img_type(string $file_type)
{
    $required_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file_type, $required_types)) {
        return 'Загрузите картинку в одном из допустимых форматов: JPG PNG GIF';
    }
    return true;
}

function photo_validate(array $upload_photo)
{
    $tmp_name = $upload_photo['tmp_name'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($file_info, $tmp_name);
    return check_img_type($file_type) !== true ? check_img_type($file_type) : '';
}

function photo_link_validate(string $photo_link)
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

function upload_photo(array $upload_photo)
{
    $tmp_name = $upload_photo['tmp_name'];
    $path = $upload_photo['name'];
    $filename = 'uploads/' . uniqid() . '.' . pathinfo($path, PATHINFO_EXTENSION);
    move_uploaded_file($tmp_name, $filename);
    return $filename;
}

function get_tag_id($link, string $tag, array $tags)
{
    foreach ($tags as $item) {
        if ($item['tag'] === $tag) {
            return $item['id'];
        }
    }
    $sql = 'INSERT INTO tags (tag) VALUE (?)';
    $stmt = db_get_prepare_stmt($link, $sql, [$tag]);
    $result = mysqli_stmt_execute($stmt);
    return $result ? mysqli_insert_id($link) : false;
}

function create_post_tag_sql($link, int $post_id, array $array_id)
{
    foreach ($array_id as $item) {
        $request_values[] = '(' . $post_id . ', ' . $item . ')';
    }
    $request_string = implode(', ', $request_values);

    $sql = 'INSERT INTO post_tag (post_id, tag_id) VALUES'
        . $request_string;
    $result = mysqli_query($link, $sql);

    return $result ? true : exit('error' . mysqli_error($link));
}

function validate_post_rules($post_type, $tags)
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
