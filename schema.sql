CREATE DATABASE readme
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;
USE readme;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_email VARCHAR (320) UNIQUE,
    user_login VARCHAR (60) UNIQUE,
    user_password VARCHAR(60),
    user_pic CHAR
);

CREATE TABLE posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    post_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    post_title VARCHAR(60),
    post_content TEXT,
    post_auth_quote VARCHAR(60),
    post_img CHAR,
    post_video CHAR,
    post_link CHAR,
    view_count INT,
    post_auth INT REFERENCES users (user_id),
    post_type INT REFERENCES post_types (post_type_id),
    post_teg INT REFERENCES tags (tag_id)
);

CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    comment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comment_content TEXT,
    comment_auth INT REFERENCES users (user_id),
    comment_post INT REFERENCES posts (post_id)
);

CREATE TABLE likes (
    like_id INT AUTO_INCREMENT PRIMARY KEY,
    like_user INT REFERENCES users (user_id),
    like_post INT REFERENCES posts (post_id)
);

CREATE TABLE subs (
    sub_id INT AUTO_INCREMENT PRIMARY KEY,
    sub_user INT REFERENCES users (user_id),
    sub_post INT REFERENCES posts (post_id)
);

CREATE TABLE massages (
    massage_id INT AUTO_INCREMENT PRIMARY KEY,
    massage_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    massage_content TEXT,
    massage_sender INT REFERENCES users (user_id),
    massage_addressee INT REFERENCES users (user_id)
);

CREATE TABLE tags (
    tag_id INT AUTO_INCREMENT PRIMARY KEY,
    tag VARCHAR(30) UNIQUE
);

CREATE TABLE post_types (
    post_type_id INT AUTO_INCREMENT PRIMARY KEY,
    post_type_name VARCHAR(30) UNIQUE,
    post_type_class VARCHAR(10) UNIQUE
);




