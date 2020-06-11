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

$popular_posts = [
    [
        'title' => 'Цитата',
        'post_type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'user_name' => 'Лариса',
        'user_pic' => 'userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Игра престолов',
        'post_type' => 'post-text',
        'content' => 'Не могу дождаться начала финального сезона своего любимого сериала!',
        'user_name' => 'Владик',
        'user_pic' => 'userpic.jpg'
    ],
    [
        'title' => 'Наконец, обработал фотки!',
        'post_type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'user_name' => 'Виктор',
        'user_pic' => 'userpic-mark.jpg'
    ],
    [
        'title' => 'Моя мечта',
        'post_type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'user_name' => 'Лариса	',
        'user_pic' => 'userpic-larisa-small.jpg'
    ],
    [
        'title' => 'Лучшие курсы',
        'post_type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'user_name' => 'Владик',
        'user_pic' => 'userpic.jpg'
    ]
];

$page_content = include_template('main.php', ['popular_posts' => $popular_posts]);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: популярное',
    'user_name' => 'Nadiia',
    'is_auth' => rand(0, 1)
]);

print ($layout_content);
