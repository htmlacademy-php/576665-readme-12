<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

check_page_access();

$current_user = $_SESSION['user'];
$current_user_id = (int)$current_user['id'];
$messages = get_messages($link, $current_user['id']);
$contacts = [];
$contacts_messages = [];

foreach ($messages as $message) {
    if ($message['user_sender_id'] !== $current_user['id']) {
        if (!isset($contacts[$message['user_sender_id']])) {
            $contacts[$message['user_sender_id']] = array(
                'name' => $message['sender_name'],
                'picture' => $message['sender_picture'],
            );
        }
        $contacts_messages[$message['user_sender_id']][] = array(
            'message' => $message['content'],
            'date' => $message['date'],
            'my_message' => false,
        );
    }

    if ($message['user_recipient_id'] !== $current_user['id']) {
        if (!isset($contacts[$message['user_recipient_id']])) {
            $contacts[$message['user_recipient_id']] = array(
                'name' => $message['recipient_name'],
                'picture' => $message['recipient_picture'],
            );
        }
        $contacts_messages[$message['user_recipient_id']][] = array(
            'message' => $message['content'],
            'date' => $message['date'],
            'my_message' => true,
        );
    }
}
foreach ($contacts as $contact_id => $contact) {
    $contact['last_message'] = '';
    if (!empty($contacts_messages[$contact_id])) {
        $last_message_key = array_key_last($contacts_messages[$contact_id]);
        $contacts[$contact_id]['last_message'] = $contacts_messages[$contact_id][$last_message_key];
    }
}

$current_contact = filter_input(INPUT_GET, 'contact_id', FILTER_VALIDATE_INT) ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_message = filter_input_array(INPUT_POST, [
        'content' => FILTER_DEFAULT,
        'recipient_id' => FILTER_VALIDATE_INT
    ] , true);
    $recipient_id = filter_input(INPUT_POST, 'recipient_id', FILTER_VALIDATE_INT);
    $new_message['content'] = trim($new_message['content']);
    $rules = [
        'content' => function($value) {
        return check_emptiness($value);
        },
        'recipient_id' => function($value) {
        return is_user_exist($link, $value)===false ? 'Такой пользователь не существует' : '';
    }
    ];
    $errors = check_data_by_rules($new_message, $rules);
    var_dump($errors);

    if (empty($errors)) {
        $sql = 'INSERT INTO messages (content, user_sender_id, user_recipient_id) VALUES (?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [
            $new_message,
            $current_user_id,
            $recipient_id
        ]);
        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
            exit ('error' . mysqli_error($link));
        }
        header("Location: /messages.php?contact_id=" . $recipient_id);
        exit ();
    }
}

$page_content = include_template('messages.php', [
    'current_user' => $current_user,
    'current_contact' => $current_contact,
    'contacts' => $contacts,
    'contacts_messages' => $contacts_messages,
    'errors' => $errors ?? ''
]);

$layout = include_template('layout.php', [
    'current_user' => $current_user,
    'content' => $page_content,
    'title' =>  'readme: мой профиль'
]);

print $layout;
