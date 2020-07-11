<?php

function cut_text ($text, $excerpt_length = 300)
{
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

function esc ($str)
{
    $text = htmlspecialchars($str);
    return $text;
}

function relative_date ($post_date)
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
    } elseif ($diff->y == 0 && $diff->m == 0 && $diff->d >=7 && $diff->d < 35) {
        $diff_weeks = $diff->d/7;
        echo $diff_weeks . ' ' . get_noun_plural_form($diff_weeks, 'неделя', 'недели', 'недель') . ' ' . 'назад';
    } else {
        $diff_m = $diff->y*12 + $diff->m;
        echo $diff_m . ' ' . get_noun_plural_form($diff_m, 'месяц', 'месяца', 'месяцев') . ' ' . 'назад';
    }
}

require_once 'init.php';
require_once 'helpers.php';

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
    $param_sort = 'view_count';

    $query_type = filter_input(INPUT_GET, 'post_type');

    if ($query_type) {
        $param_type = $query_type;
    }

    $sql = 'SELECT * , users.id, post_types.id FROM posts'
        . ' JOIN users ON posts.user_id = users.id'
        . ' JOIN post_types ON posts.post_type_id = post_types.id';

    if ($param_type) {
        $sql .= " WHERE posts.post_type_id =" . $param_type;
    }

    if ($param_sort) {
        $sql .= " ORDER BY " . $param_sort . " DESC LIMIT 6 ";
    }

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
    'param_type' => $param_type,
    'param_sort' => $param_sort
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: популярное',
    'user_name' => 'Nadiia',
    'is_auth' => rand(0, 1)
]);

print ($layout_content);
