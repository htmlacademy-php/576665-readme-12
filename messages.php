<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

check_page_access();

$current_user = $_SESSION['user'];
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
    var_dump($messages);
    var_dump($contacts);
    var_dump($contacts_messages);

$current_contact = filter_input(INPUT_GET, 'message_to', FILTER_VALIDATE_INT) ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_message = filter_input(INPUT_POST, 'new_message', FILTER_DEFAULT);
    $new_message = trim($new_message);
    $rules = [
        'new_message' => function($value) {
        return check_emptiness($value);
        }
    ];
    $errors = check_data_by_rules(['new_message' => $new_message], $rules);

    if (empty($errors)) {
        $sql = 'INSERT INTO messages (content, user_sender_id, user_recipient_id) VALUES (?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [
            $new_message,
            $current_user['id'],
            $current_contact
        ]);
    }
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
}

$page_content = include_template('messages.php', [
    'current_user' => $current_user,
    'current_contact' => $current_contact,
    'contacts' => $contacts,
    'contacts_messages' => $contacts_messages
]);

$layout = include_template('layout.php', [
    'current_user' => $current_user,
    'content' => $page_content,
    'title' =>  'readme: мой профиль'
]);

print $layout;
