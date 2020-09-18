CREATE DATABASE readme
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;

USE readme;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR (320) UNIQUE,
    login VARCHAR (60) UNIQUE,
    password VARCHAR(60),
    picture VARCHAR (320)
);

CREATE TABLE posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    title VARCHAR(60),
    content TEXT,
    author_quote VARCHAR(160),
    img VARCHAR(160),
    video VARCHAR(160),
    link VARCHAR(160),
    view_count INT DEFAULT 0,
    user_id INT REFERENCES users (id),
    post_type_id INT REFERENCES post_types (id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    content TEXT,
    user_id INT REFERENCES users (id),
    post_id INT REFERENCES posts (post_id)
);

CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT REFERENCES users (id),
    post_id INT REFERENCES posts (post_id)
);

CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT REFERENCES users (id),
    follower_id INT REFERENCES users (id)
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    content TEXT,
    user_sender_id INT REFERENCES users (id),
    user_recipient_id INT REFERENCES users (id)
);

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tag VARCHAR(30) UNIQUE
);

CREATE TABLE post_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) UNIQUE,
    class VARCHAR(10) UNIQUE
);

CREATE TABLE post_tag (
post_id INT REFERENCES posts (post_id),
tag_id INT REFERENCES tags (id),
primary key (post_id, tag_id)
);

CREATE INDEX view_count ON posts (view_count);

CREATE INDEX date ON posts (date);

CREATE FULLTEXT INDEX post_ft_search ON posts(title, content);

