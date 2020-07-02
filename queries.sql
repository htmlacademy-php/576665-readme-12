INSERT INTO post_types (name, class)
VALUES
('Текст', 'text'),
('Картинка', 'photo'),
('Видео', 'video'),
('Цитата', 'quote'),
('Ссылка', 'link');

INSERT INTO users (email, login, password, picture)
VALUES
('lara@mail.com', 'Лариса', 'lara', 'img/userpic-larisa-small.jpg'),
('vlad@mail.com', 'Владик', 'vlad', 'img/userpic.jpg'),
('victor@mail.com', 'Виктор', 'victor', 'img/userpic-mark.jpg');

INSERT INTO comments (content, user_id, post_id)
VALUES
('Magnificent', 1, 1),
('Trash!!!', 1, 2);

INSERT INTO posts (date, title, content, author_quote, img, video, link, view_count, user_id, post_type_id)
VALUES
('2020-06-23 20:47', 'Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный Автор', '', '', '', 1, 1, 4),
('2020-06-23 15:11', 'Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', '', '', '', '', 5, 2, 1),
('2020-06-19 21:11', 'Наконец, обработал фотки!', '', '', 'img/rock-medium.jpg', '', '', 1, 3, 2),
('2020-06-09 21:11', 'Моя мечта', '', '', 'img/coast-medium.jpg', '', '', 1, 1, 2),
('2020-02-23 21:11', 'Лучшие курсы', '', '', '', '', 'www.htmlacademy.ru', 1, 2, 4);

SELECT p.*, u.login, pt.name
FROM posts p
JOIN users u ON p.user_id = u.id
JOIN post_types pt ON p.post_type_id = pt.id
ORDER BY view_count DESC;

SELECT *
FROM posts
WHERE user_id = '2';

SELECT c.*, u.login
FROM comments c
JOIN users u ON c.user_id = u.id
WHERE post_id = 1;

INSERT INTO likes (user_id, post_id)
VALUES (1, 2);

INSERT INTO subscriptions (user_id, post_id)
VALUES (1, 2);