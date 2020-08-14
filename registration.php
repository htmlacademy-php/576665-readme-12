<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $registration_data = [];

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

    $rules = [];
    $rules ['email'] = function ($value) {
        return email_validate($value);
    };
    $rules['login'] = function ($value) {
        return check_emptiness($value);
    };
    $rules['password'] = function ($value) {
        return check_emptiness($value);
    };
    $rules['password_repeat'] = function ($value) {
        return check_emptiness($value);
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

    $unique_values = ['email', 'login'];

    foreach ($unique_values as $item) {
        if (empty($errors[$item])) {
            $errors[$item] = !check_unique_user($link, $registration_data[$item],
                $item) ? "Указанный {$item} уже используется другим пользователем" : '';
        }
    }

    if (empty($errors['password_repeat'])) {
        $errors['password_repeat'] = check_password_repeat($registration_data['password_repeat'], $registration_data['password']);
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

    if (empty($errors)) {
        $registration_data['password'] = password_hash($registration_data['password'], PASSWORD_DEFAULT);
        $registration_data['avatar'] = !empty($_FILES['userpic-file']['name']) ? upload_photo($_FILES['userpic-file']) : '';

        $sql = 'INSERT INTO users (email, login, password, picture) VALUE (?, ?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [
            $registration_data['email'],
            $registration_data['login'],
            $registration_data['password'],
            $registration_data['avatar']
        ]);

        $result = mysqli_stmt_execute($stmt);

        if(!$result) {
            exit('error' . mysqli_error($link));
        }
        header('Location: /');
    }
}

$page_content = include_template('registration.php', [
    'registration_data' => !empty($registration_data) ? $registration_data : '',
    'errors' => !empty($errors) ? $errors : '',
    'error_titles' => !empty($error_titles) ? $error_titles : ''

]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'readme: регистрация',
]);

print ($layout_content);

