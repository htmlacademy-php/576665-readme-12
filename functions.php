<?php

/**
 * Converts special characters to HTML entities
 * @param string $str
 *
 * @return string The converted string
 */
function esc(string $str)
{
    return htmlspecialchars($str, ENT_QUOTES);
}

/**
 * Сut string to a character length, the default is 80,
 * adds "..." at the end of excerpt
 * @param string $text
 * @param int $excerpt_length Maximum allowed length
 *
 * @return string The original string if its length is less than
 * maximum allowed length or excerpt
 */
function cut_text(string $text, int $excerpt_length = 80)
{
    $text_length = mb_strlen($text);
    if ($text_length > $excerpt_length) {
        $text = mb_substr($text, 0, $excerpt_length);
        $text = mb_substr($text, 0, mb_strrpos($text, ' ')) . '...';
    }
    return $text;
}

/**
 * Returns a single number of months, days, weeks, hours, minutes
 * between the current date and the post publication date
 * @param string $post_date Date in 'Y-m-d H:i:s' format
 *
 * @return string The date in relative terms or string 'меньше минуты назад'
 * if post was created less then 60 seconds ago
 */
function get_relative_date(string $post_date)
{
    $publish_date = date_create($post_date);
    $cur_date = date_create('now');
    $diff = date_diff($cur_date, $publish_date);
    if ($diff->m >= 1) {
        $diff_months = $diff->m;
        return "$diff_months " . get_noun_plural_form($diff_months, 'месяц', 'месяца', 'месяцев');
    }
    if ($diff->d >= 7) {
        $diff_weeks = floor(($diff->d) / 7);
        return "$diff_weeks " . get_noun_plural_form($diff_weeks, 'неделя', 'недели', 'недель');
    }
    if ($diff->d < 7 && $diff->d >= 1) {
        $diff_days = $diff->d;
        return "$diff_days " . get_noun_plural_form($diff_days, 'день', 'дня', 'дней');
    }
    if ($diff->h >= 1) {
        $diff_hours = $diff->h;
        return "$diff_hours " . get_noun_plural_form($diff_hours, 'час', 'часа', 'часов');
    }
    if ($diff->i >= 1) {
        $diff_minutes = $diff->i;
        return "$diff_minutes " . get_noun_plural_form($diff_minutes, 'минута', 'минуты', 'минут');
    }
    return 'меньше минуты';
}

/**
 * Return post types associative array
 * @param mysqli $link The MySQL connection
 *
 * @return array The associative array of post types
 */
function get_post_types(mysqli $link)
{
    $result = mysqli_query($link, "SELECT * FROM post_types");
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Returns class-name of post type by id
 * @param mysqli $link
 * @param string $id Post type ID
 *
 * @return string | null The post type class-name or null if ID is not exist
 */
function get_active_post_type( $link, string $id)
{
    $sql = "SELECT class FROM post_types WHERE post_types.id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_assoc($result)['class'] ?? '';
}

/**
 * Returns error message if string is empty
 * @param string $value
 *
 * @return string Error message or empty string
 */
function check_emptiness(string $value)
{
    return empty($value) ? 'Это поле должно быть заполнено' : '';
}

/**
 * Checks whether a string is a link to a Youtube video
 * @param string $value video url
 *
 * @return string Error message or empty string if url is correct
 */
function check_youtube_domain(string $value)
{
    $domain = parse_url($value, PHP_URL_HOST);
    return strpos($domain, 'youtube.com') === false ? 'Введите ссылку на видео из YOUTUBE' : '';
}

/**
 * Checks whether a user value is exists in database
 * @param mysqli $link The MySQL connection
 * @param string $value
 * @param string $param
 *
 * @return bool true if value is unique or false if a value is exists in database
 */
function check_unique_user($link, string $value, string $param)
{
    $sql = "SELECT id FROM users WHERE {$param} = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$value]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return (mysqli_num_rows($result) > 0) ? false : true;
}

/**
 * Checks whether a string is valid email
 * @param string $email
 *
 * @return bool true if email is correct or false if email is not valid
 */
function is_valid_email(string $email)
{
    return (filter_var($email, FILTER_VALIDATE_EMAIL)) ? true : false;
}

/**
 * Checks whether password_repeat value and the password do match
 * @param string $value The password_repeat value
 * @param string $password The password value
 *
 * @return string Error message or empty string if passwords do match
 */
function check_password_repeat (string $value, $password)
{
    return  ($value !== $password) ? 'Пароли не совпадают' : '';
}

/**
 * Checks whether the email value is correct
 * @param string $email
 * @return string Error message or empty string if value is correct
 */
function email_validate (string $email)
{
    if (empty($email)) {
        return "Это поле должно быть заполнено";
    }
    if (!is_valid_email($email)) {
        return "Адрес электронной почты не корректен";
    }
    return '';
}

/**
 * Checks whether a string is a correct link
 * @param string $link_value
 *
 * @return string Error message or empty string if link is correct
 */
function link_validate(string $link_value)
{
    if (empty($link_value)) {
        return "Это поле должно быть заполнено";
    } elseif (!filter_var($link_value, FILTER_VALIDATE_URL)) {
        return "Значение не является ссылкой";
    }
}

/**
 * Validates a string contains tags
 * @param string $tags_value
 *
 * @return string Error massage or empty string if string contains correct tags
 */
function tags_validate(string $tags_value)
{
    $invalid_tags = [];
    $tag_error = '';
    $tags_array = explode(' ', $tags_value);
    if (!empty($tags_value)) {
        foreach ($tags_array as $tag) {

            if (!preg_match('/^[a-zA-Zа-яёА-ЯЁ0-9]+$/u', $tag)) {
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

/**
 * Validates link to a video
 * @param string $url_value
 *
 * @return string Error message or empty string if link is correct
 */
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

/**
 * Checks image's MIME-type
 * @param string $file_type File's MIME-type
 *
 * @return bool|string Error message or true if MIME-type is correct
 */
function check_img_type(string $file_type)
{
    $required_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file_type, $required_types)) {
        return 'Загрузите картинку в одном из допустимых форматов: JPG PNG GIF';
    }
    return true;
}

/**
 * Validates uploaded file
 * @param array $upload_photo Array $_FILE
 *
 * @return bool|string Error message or true if file is correct
 */
function photo_validate(array $upload_photo)
{
    $tmp_name = $upload_photo['tmp_name'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($file_info, $tmp_name);
    return check_img_type($file_type) !== true ? check_img_type($file_type) : '';
}

/**
 * Validates link to uploaded file
 * @param string $photo_link The link to image files
 *
 * @return string Error massage or empty string if uploaded file is validate
 */
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

/**
 * Uploads file
 * @param array $upload_photo Array $_FILE
 *
 * @return string Path to uploaded file
 */
function upload_photo(array $upload_photo)
{
    $tmp_name = $upload_photo['tmp_name'];
    $path = $upload_photo['name'];
    $filename = 'uploads/' . uniqid() . '.' . pathinfo($path, PATHINFO_EXTENSION);
    move_uploaded_file($tmp_name, $filename);
    return $filename;
}

/**
 * Returns tag's ID
 * @param mysqli $link The MySQL connection.
 * @param string $tag The string contains tags
 * @param array $tags The array of exist tags
 *
 * @return bool|int false if ID is not exist or tag's ID
 */
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

/**
 * Inserts a new rows post_tag into the database
 * @param mysqli $link The MySQL connection.
 * @param int $post_id Current post ID
 * @param array $tags_id Tags ID array for current post
 *
 * @return bool|string true if tags added or error massage
 */
function create_post_tag_sql($link, int $post_id, array $tags_id)
{
    foreach ($tags_id as $item) {
        $request_values[] = "({$post_id}, {$item})";
    }
    $request_string = implode(', ', $request_values);

    $sql = 'INSERT INTO post_tag (post_id, tag_id) VALUES'
        . $request_string;
    $result = mysqli_query($link, $sql);

    return $result ? true : ('Не удалось добавить новый тег' . mysqli_error($link));
}

/**
 * Prepares validation rules for the new post
 * @param string $post_type The class-name of post type
 * @param string $tags The string contains tags
 *
 * @return array The array of validation rules
 */
function validate_post_rules($post_type, $tags)
{
    $rules = [];
    $rules['title'] = function ($value) {
        return check_emptiness($value);
    };
    if (!empty($tags)) {
        $rules['tags'] = function ($value) {
            return tags_validate($value);
        };
    }
    switch ($post_type) {
        case 'text':
            $rules['content'] = function ($value) {
                return check_emptiness($value);
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
                return check_emptiness($value);
            };
            $rules['author_quote'] = function ($value) {
                return check_emptiness($value);
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

/**
 * Checks data array by rules array and creates errors array
 * @param array $data_array The data array
 * @param array $rules The rules array
 *
 * @return array The errors array
 */
function check_data_by_rules($data_array, $rules) {
    $errors = [];
    foreach ($data_array as $key => $value) {
        if (isset($rules[$key])) {
            $errors[$key] = $rules[$key]($value);
        }
    }
    return array_filter($errors);
}

/**
 * Return posts tags array, using post_id as keys
 * @param mysqli $link The MySQL connection
 * @param array $posts_id Array of the given posts id
 * @return array an associative array of tags as
 * key is post's id and value is post's tags array
 */
function get_posts_tags(mysqli $link, array $posts_id) {
    $posts_id_string = implode(', ', $posts_id);
    $sql = "SELECT post_tag.post_id, tags.tag
    FROM tags
    JOIN post_tag ON post_tag.tag_id = tags.id
    WHERE post_tag.post_id IN ({$posts_id_string})";
    $result = mysqli_query($link, $sql);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    $tags = mysqli_fetch_all($result,MYSQLI_ASSOC);

    $post_tags = [];

    foreach ($tags as $key => $value) {
        $post_tags[$value['post_id']][] = $value['tag'];
    }
    return $post_tags;
}

/**
 * Return user's data by user_id
 * @param mysqli $link The MySQL connection
 * @param int $user_id
 * @return array || null the user data array or null if user_id is not exist
 */
function get_user_data(mysqli $link,  int $user_id)
{
    $sql = 'SELECT users.*,
       (SELECT COUNT(post_id) FROM posts WHERE posts.user_id = users.id) as posts_count,
       (SELECT COUNT(subscriptions.id) FROM subscriptions WHERE subscriptions.author_id = users.id) as followers_count
        FROM users
        WHERE users.id = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        exit ('errorSQL' . mysqli_error($link));
    }
    return mysqli_fetch_assoc($result);
}

/**
 * Select posts by values of parameters
 * @param mysqli $link The MySQL connection
 * @param array $params The array as keys is parameters and values is string of required values
 * @param string $order_by The field on which the sorting is to be performed, the default is 'date'
 * @param string $order The sorting order, the default is 'DESC'
 * @return array The array of selected posts
 */
function get_posts_by_parameters (mysqli $link, array $params, string $order_by = 'date', string $order = 'DESC')
{
    $sql = "SELECT posts.*, post_types.class, users.login, users.picture,
        (SELECT COUNT(likes.id) FROM likes WHERE likes.post_id = posts.post_id) as likes_count,
        (SELECT COUNT(comments.id) FROM comments WHERE comments.post_id = posts.post_id) as comments_count
        FROM posts
        JOIN post_types ON posts.post_type_id = post_types.id
        JOIN users ON users.id = posts.user_id ";

    $conditions = [];

    foreach ($params as $key => $value) {
        if (!empty($value)) {
            $conditions[] = "posts.{$key} IN ({$value})";
        }
    };

    if (!empty($conditions)) {
        $sql .= "WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " ORDER by {$order_by} {$order}";

    $result = mysqli_query($link, $sql);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Adds query parameters to current query string
 * @param array $current_query Current query string
 * @param array $query_data The array of parameters which must be adding
 *
 * @return string The query string
 */
function get_query_string (array $current_query, array $query_data)
{
    return http_build_query(array_merge($current_query, $query_data));
}

function check_page_access()
{
    if (!isset($_SESSION['user'])) {
        header('Location: /index.php');
        exit();
    }
}

function get_followers ($link, $user_id)
{
    $sql = "SELECT subscriptions.*, users.id, registered, login, picture,
       (SELECT COUNT(post_id) FROM posts WHERE posts.user_id = subscriptions.follower_id) as posts_count,
       (SELECT COUNT(subscriptions.id) FROM subscriptions WHERE users.id = subscriptions.author_id) as followers_count
        FROM subscriptions
        JOIN users ON  users.id = subscriptions.follower_id
        WHERE subscriptions.author_id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function get_posts_likes ($link, $posts_id)
{
    $posts_id_string = implode(', ', $posts_id);
    $sql = "SELECT likes.user_id, likes.post_id, likes.date, users.login, users.picture, posts.post_id, posts.post_type_id, posts.img, posts.video, post_types.class
        FROM likes
        JOIN users ON  users.id = likes.user_id
        JOIN posts ON posts.post_id = likes.post_id
        JOIN post_types ON posts.post_type_id = post_types.id
        WHERE likes.post_id IN ({$posts_id_string})
        ORDER BY likes.date DESC";
    $result = mysqli_query($link, $sql);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
