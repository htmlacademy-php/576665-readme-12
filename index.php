<?php
/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function cut_text ($text, $excerpt_length = 300) {
    $text_length = mb_strlen($text);
    if ($text_length > $excerpt_length) {
        //cut string
        $text = mb_substr($text, 0, $excerpt_length);
        //cut to last space
        $text = mb_substr($text, 0, mb_strrpos($text,' '));
        //add '...' and link to full post
        $text = '<p>' . $text . '...' . '</p>' . '<a class="post-text__more-link" href="#">Читать далее</a>';
    } else {
        $text = '<p>' . $text . '</p>';
    }
    return  $text;
}

function esc ($str) {
    $text = htmlspecialchars($str);
    return $text;
}

/**
 * set the default timezone
 */
date_default_timezone_set('Europe/Moscow');

/**
 * @param $index
 * @return false|string
 */
function generate_random_date($index)
{
    $deltas = [['minutes' => 59], ['hours' => 23], ['days' => 6], ['weeks' => 4], ['months' => 11]];
    $deltas_count = count($deltas);

    if ($index < 0) {
        $index = 0;
    }

    if ($index >= $deltas_count) {
        $index = $deltas_count - 1;
    }

    $delta = $deltas[$index];
    $timeval = rand(1, current($delta));
    $timename = key($delta);

    $ts = strtotime("$timeval $timename ago");
    $dt = date('Y-m-d H:i:s', $ts);

    return $dt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
{
    $number = (int)$number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

function relative_date ($post_date) {

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
    } elseif ($diff->y == 0 && $diff->m == 0 && $diff->d >=7 && $diff->d < 35) {
        $diff_weeks = $diff->d/7;
        echo $diff_weeks . ' ' . get_noun_plural_form($diff_weeks, 'неделя', 'недели', 'недель') . ' ' . 'назад';
    } else {
        $diff_m = $diff->y*12 + $diff->m;
        echo $diff_m . ' ' . get_noun_plural_form($diff_m, 'месяц', 'месяца', 'месяцев') . ' ' . 'назад';
    }
}

require_once 'init.php';

if (!$link) {
    print ('error' . mysqli_connect_error());
} else {
    $sql = 'SELECT id, name, class FROM post_types';
    $result = mysqli_query($link, $sql);
    if ($result) {
        $post_types = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        print ('error ' . mysqli_error($link));
    }

    $param_type = '';
    $query_string = '';

    $query_type = filter_input(INPUT_GET, 'post_type');

    if ($query_type) {
        $param_type = $query_type;
        $query_string = " WHERE posts.post_type_id =" . $param_type;
    }

    $sql = 'SELECT * , users.id, post_types.class FROM posts'
        . ' JOIN users ON posts.user_id = users.id'
        . ' JOIN post_types ON posts.post_type_id = post_types.id'
        .  $query_string
        . ' ORDER BY view_count DESC LIMIT 6';


    $result = mysqli_query($link, $sql);
    if ($result) {
        $popular_posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        print ('error' . mysqli_error($link));
    }
}

$page_content = include_template('main.php', [
    'popular_posts' => $popular_posts,
    'post_types' => $post_types,
    'param_type' => $param_type
]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: популярное',
    'user_name' => 'Nadiia',
    'is_auth' => rand(0, 1)
]);

print ($layout_content);
