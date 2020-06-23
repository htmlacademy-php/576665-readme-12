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
('It\'s trash', 1, 2);

INSERT INTO posts (date, title, content, author_quote, img, video, link, view_count, user_id, post_type_id, tag_id)
VALUES
('2020-06-23 20:47', 'Цитата', 'Мы в жизни любим только раз, а после ищем лишь похожих', 'Неизвестный Автор', '', '', '', '1', '1', '4', 'NULL'),
('2020-06-23 15:11', 'Игра престолов', 'Не могу дождаться начала финального сезона своего любимого сериала!', '', '', '', '', '1', '2', '1', 'NULL'),
('2020-06-19 21:11', 'Наконец, обработал фотки!', '', '', 'img/rock-medium.jpg', '', '', '1', '3', '2', 'NULL'),
('2020-06-09 21:11', 'Моя мечта', '', '', 'img/coast-medium.jpg', '', '', '1', '1', '2', 'NULL'),
('2020-02-23 21:11', 'Лучшие курсы', '', '', '', '', 'www.htmlacademy.ru', '1', '2', '4', 'NULL');

