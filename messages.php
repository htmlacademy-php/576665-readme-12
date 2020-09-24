<?php

require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

check_page_access();

$current_user = $_SESSION['user'];
$current_user_id = (int)$current_user['id'];
$messages = get_messages($link, $current_user['id']); //массив всех сообщений, где получателем или отправителем является текущий пользователь
$contacts = []; //список всех собеседников
$contacts_messages = []; //массив, где ключ - айди собеседника, значение - массив сообщений (переписка)

$current_contact = isset($_GET['contact_id']) //текущий собеседник
    ? filter_input(INPUT_GET, 'contact_id', FILTER_VALIDATE_INT)
    : filter_input(INPUT_POST, 'recipient_id', FILTER_VALIDATE_INT);


if ($current_contact !== null && !is_user_exist($link, $current_contact, 'id')) {
    header('HTTP/1.0 404 Not Found');
    exit();
}
//если линк не содержит запрос (переход на страницу из меню юзера)
if (!empty($current_contact)) {
    $contacts[$current_contact] = get_user_data($link, $current_contact);
}
//если открыта вкладка собеседника, то все входящие сообщения обретают статус просмотреных
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = 'UPDATE messages SET viewed = 1 WHERE user_recipient_id = ? AND user_sender_id = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$current_user_id, $current_contact]);
    $result = mysqli_stmt_execute($stmt);
    if (!$result) {
        exit ('error'.mysqli_error($link));
    }
}

//создает массив собеседников $contacts, где ключ - айди собеседника, значение - массив данных собеседника
// и массив сообщений $contacts_messages, где ключ - айди собеседника, значение - массив сообщений (переписка с собеседником)
if (!empty($messages)) {
    foreach ($messages as $message) {
        if ($message['user_sender_id'] !== $current_user['id']) {
            if (!isset($contacts[$message['user_sender_id']])) {
                $contacts[$message['user_sender_id']] = array(
                    'login' => $message['sender_name'],
                    'picture' => $message['sender_picture'],
                );
            }
            $contacts_messages[$message['user_sender_id']][] = $message;
        }

        if ($message['user_recipient_id'] !== $current_user['id']) {
            if (!isset($contacts[$message['user_recipient_id']])) {
                $contacts[$message['user_recipient_id']] = array(
                    'login' => $message['recipient_name'],
                    'picture' => $message['recipient_picture'],
                );
            }
            $contacts_messages[$message['user_recipient_id']][] = $message;
        }
    }
    //добавляет каждому элементу массива собеседников $contacts данные последнего сообщения в переписке (для вывода на вкладку собеседника)
    //и количество непрочитанных сообщений
    foreach ($contacts as $contact_id => $contact) {
        $contact['last_message'] = '';
        if (!empty($contacts_messages[$contact_id])) {
            $last_message_key = array_key_last($contacts_messages[$contact_id]);
            $contacts[$contact_id]['last_message'] = $contacts_messages[$contact_id][$last_message_key];
            $contacts[$contact_id]['unread_count'] = get_unread_messages_count($link, $contact_id, $current_user_id);
        }
    }
    //получает общее количество непрочинанных сообщений
    $total_unread_messages = array_sum(array_column($contacts, 'unread_count'));

    //если нет активного собеседника (перешли по ссылке в меню, например), то активной окажется первая вкладка
    if (!empty($contacts) && empty($current_contact)) {
        $current_contact = array_key_first($contacts);
        header('Location:messages.php?contact_id=' .  $current_contact);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_message['content'] = trim(filter_input(INPUT_POST, 'content', FILTER_DEFAULT));
    $rules = [
        'content' => function ($value) {
            return check_emptiness($value);
        },
    ];
    $errors['recipient_id'] = !is_user_exist($link, $current_contact, 'id') || $current_contact === $current_user_id ? 'Невозможно отправить сообщение этому пользователю' : '';
    $errors = check_data_by_rules($new_message, $rules);
    if (empty($errors)) {
        $sql = 'INSERT INTO messages (content, user_sender_id, user_recipient_id) VALUES (?, ?, ?)';
        $stmt = db_get_prepare_stmt($link, $sql, [
            $new_message['content'],
            $current_user_id,
            $current_contact
        ]);
        $result = mysqli_stmt_execute($stmt);
        if (!$result) {
            exit ('error' . mysqli_error($link));
        }
        header("Location: /messages.php?contact_id=" . $current_contact);
        exit ();
    }
}

$page_content = include_template('messages.php', [
    'current_user' => $current_user,
    'current_contact' => $current_contact,
    'contacts' => $contacts,
    'contacts_messages' => $contacts_messages,
    'new_message' => $new_message ?? '',
    'errors' => $errors ?? ''
]);

$layout = include_template('layout.php', [
    'current_user' => $current_user,
    'content' => $page_content,
    'title' =>  'readme: мой профиль',
    'total_unread_messages' => $total_unread_messages ?? ''
]);

print $layout;
