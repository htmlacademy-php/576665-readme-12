<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $registration_data = [];
    var_dump ($_POST);
    var_dump($_FILES);

    $registration_data = filter_input_array(INPUT_POST, [
        'email' => FILTER_DEFAULT,
        'login' => FILTER_DEFAULT,
        'password' => FILTER_DEFAULT,
        'password_repeat' => FILTER_DEFAULT
    ], true);

    foreach ($registration_data as $key => $value) {
        $registration_data[$key] = !empty($value) ? trim ($value) : '';
    }

    $registration_data['avatar'] = !empty($_FILES['userpic-file']) ? $_FILES['userpic-file'] : '';

    var_dump($registration_data);

    $rules = [];
    $rules ['email'] = function ($value) {
        return email_validate($value);
    };
    $rules['login'] = function ($value) {
        return check_text($value);
    };
    $rules['password'] = function ($value) {
        return check_text($value);
    };
    $rules['password_repeat'] = function ($value) {
        return password_repeat_validate($value);
    };
    if (!empty($_FILES['userpic-file']['name'])) {
        $rules['avatar'] = function ($value) {
            return photo_validate($value);
        };
    };

    $errors = [];

    foreach ($registration_data as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }
    }

    if (empty($errors['email'])) {
        $errors['email'] = check_unique_user($link, $registration_data['email'],
            'email') !== true ? 'Указанный email уже используется другим пользователем' : '';
    }

    if (empty($errors['login'])) {
        $errors['login'] = check_unique_user($link, $registration_data['login'],
            'login') !== true ? 'Указанный login уже используется другим пользователем' : '';
    }

    $errors = array_filter ($errors);

    if (!empty($errors)) {
        $error_titles = [
            'email' => 'Электронная почта',
            'login' => 'Логин',
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
            'avatar' => 'Аватар'
        ];
    }
    var_dump($errors);
}

$page_content = include_template('registration.php', [
    'registration_data' => !empty($registration_data) ? $registration_data : '',
    'errors' => !empty($errors) ? $errors : '',
    'error_titles' => !empty($error_titles) ? $error_titles : ''

]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: регистрация',
    'user_name' => 'Nadiia',
    'is_auth' => rand(0, 1)

]);

print ($layout_content);

