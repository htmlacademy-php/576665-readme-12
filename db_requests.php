<?php
/**
 * Return post types associative array
 * @param mysqli $link The MySQL connection
 *
 * @return array The associative array of post types
 */
function get_post_types(mysqli $link)
{
    $result = mysqli_query($link, "SELECT * FROM post_types");
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Returns class-name of post type by id
 * @param mysqli $link
 * @param string $id Post type ID
 *
 * @return string | null The post type class-name or null if ID is not exist
 */
function get_active_post_type( mysqli $link, string $id)
{
    $sql = "SELECT class FROM post_types WHERE post_types.id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_assoc($result)['class'] ?? '';
}

/**
 * Select posts by values of parameters
 * @param mysqli $link The MySQL connection
 * @param array $params The array as keys is parameters and values is string of required values
 * @param int $current_user_id User's ID
 * @param string $order_by The field on which the sorting is to be performed, the default is 'date'
 * @param string $order The sorting order, the default is 'DESC'
 * @param int|null $limit The number of posts to be returned, the default is null
 * @param int $offset The offset of the first post to returned, the default is 0
 *
 * @return array The array of selected posts
 */

function get_posts_by_parameters (mysqli $link, array $params, int $current_user_id, string $order_by = 'date',  string $order = 'DESC', int $limit = null, int $offset = 0)
{
    $sql = "SELECT posts.*, post_types.class, users.login, users.picture,
        (SELECT COUNT(likes.id) FROM likes WHERE likes.post_id = posts.post_id) as likes_count,
        (SELECT COUNT(comments.id) FROM comments WHERE comments.post_id = posts.post_id) as comments_count,
        (SELECT likes.user_id FROM likes WHERE posts.post_id = likes.post_id AND likes.user_id = {$current_user_id}) as is_liked
        FROM posts
        JOIN post_types ON posts.post_type_id = post_types.id
        JOIN users ON users.id = posts.user_id ";

    $conditions = [];

    foreach ($params as $key => $value) {
        if (!empty($value)) {
            $conditions[] = "posts.{$key} IN ({$value})";
        }
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " ORDER by {$order_by} {$order}";

    if (!empty($limit)) {
        $sql .= " LIMIT {$limit} OFFSET {$offset}";
    }

    $result = mysqli_query($link, $sql);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Counts all posts in a database, selected by parameters
 * @param mysqli $link The MySQL connection
 * @param array $params The array as keys is parameters and values is string of required values
 *
 * @return int|null The the number of posts or null if posts are not exist
 */
function get_posts_count (mysqli $link, array $params)
{
    $sql = "SELECT posts.* FROM posts ";

    $conditions = [];

    foreach ($params as $key => $value) {
        if (!empty($value)) {
            $conditions[] = "posts.{$key} IN ({$value})";
        }
    }

    if (!empty($conditions)) {
        $sql .= "WHERE " . implode(' AND ', $conditions);
    }

    $result = mysqli_query($link, $sql);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_num_rows($result);
}

function get_repost_data($link, $original_post_id)
{
    $sql = "SELECT posts.user_id, users.login, users.picture FROM posts 
        JOIN users ON users.id = posts.user_id
        WHERE posts.post_id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$original_post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_assoc($result);
}

/**
 * Returns array of post's comments
 * @param mysqli $link The MySQL connection
 * @param int $post_id The post's ID
 *
 * @return array The array of post's comments or null if comments are not exist
 */
function get_comments(mysqli $link, int $post_id)
{
    $sql = "SELECT *
    FROM comments
    JOIN users on users.id = comments.user_id
    WHERE comments.post_id = ?
    ORDER BY date DESC";
    $stmt = db_get_prepare_stmt($link, $sql, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Returns array of messages
 * @param mysqli $link The MySQL connection
 * @param int $user_id The user's ID
 *
 * @return array The array of messages where user is sender or recipient
 */
function get_messages(mysqli $link, int $user_id)
{
    $sql = "SELECT messages.*,
        sender.login as sender_name,
        sender.picture as sender_picture,
        recipient.login as recipient_name,
        recipient.picture as recipient_picture FROM messages
        JOIN users sender ON user_sender_id = sender.id
        JOIN users recipient ON user_recipient_id = recipient.id
        WHERE messages.user_sender_id = ? || messages.user_recipient_id = ?
        ORDER BY messages.date";

    $stmt = db_get_prepare_stmt($link, $sql, [$user_id, $user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Checks whether user is follower of author or not
 * @param mysqli $link The MySQL connection
 * @param int $user_id The current user ID
 * @param int $author_id The author's ID
 *
 * @return bool True if current user is follower, false otherwise
 */
function is_following (mysqli $link, int $user_id, int $author_id)
{
    $sql = "SELECT subscriptions.*
        FROM subscriptions
        WHERE subscriptions.follower_id = ? AND subscriptions.author_id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$user_id, $author_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result) ? true : false;
}

/**
 * Returns data array of likes to posts by posts IDs
 * @param mysqli $link The MySQL connection
 * @param array $posts_id The array of the posts IDs
 *
 * @return array The array of likes data, sorting by date
 */
function get_posts_likes (mysqli $link, array $posts_id)
{
    $posts_id_string = implode(', ', $posts_id);
    $sql = "SELECT likes.user_id, likes.post_id, likes.date, users.login, users.picture, posts.post_id, posts.post_type_id, posts.img, posts.video, post_types.class
        FROM likes
        JOIN users ON  users.id = likes.user_id
        JOIN posts ON posts.post_id = likes.post_id
        JOIN post_types ON posts.post_type_id = post_types.id
        WHERE likes.post_id IN ({$posts_id_string})
        ORDER BY likes.date DESC";
    $result = mysqli_query($link, $sql);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Checks whether post is exist
 * @param mysqli $link The MySQL connection
 * @param int $post_id The post's ID
 *
 * @return bool True if post is exist, false otherwise
 */
function is_post_exist(mysqli $link, int $post_id)
{
    $sql = "SELECT * FROM posts WHERE post_id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}

/**
 * Checks  whether post is liked by current user
 * @param mysqli $link The MySQL connection
 * @param int $post_id The post's ID
 * @param int $current_user_id The current user's ID
 *
 * @return bool True if post was liked by user, false otherwise
 */
function is_liked(mysqli $link, int $post_id, int $current_user_id) {
    $sql = "SELECT likes.*
        FROM likes
        WHERE likes.post_id = ? AND likes.user_id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$post_id, $current_user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result) ? true : false;
}

/**
 * Returns unread messages count
 * @param mysqli $link The MySQL connection
 * @param int $sender_id The sender's ID
 * @param int $current_user_id The recipient's ID
 * @return int The unread messages count
 */
function get_unread_messages_count(mysqli $link, int $sender_id, int $current_user_id)
{
    $sql = "SELECT messages.* FROM messages WHERE user_sender_id = ? AND user_recipient_id = ? AND viewed = 0";
    $stmt = db_get_prepare_stmt($link, $sql, [$sender_id, $current_user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_num_rows($result);
}

/**
 * Returns the data of users who follow the user by user_id
 * @param mysqli $link The MySQL connection
 * @param string $user_id User's ID
 *
 * @return array The array data of users who follow the user
 */
function get_followers(mysqli $link, string $user_id)
{
    $sql = "SELECT subscriptions.*, users.id, users.registered, users.email, users.login, picture,
       (SELECT COUNT(post_id) FROM posts WHERE posts.user_id = subscriptions.follower_id) as posts_count,
       (SELECT COUNT(subscriptions.id) FROM subscriptions WHERE users.id = subscriptions.author_id) as followers_count
        FROM subscriptions
        JOIN users ON  users.id = subscriptions.follower_id
        WHERE subscriptions.author_id = ?";
    $stmt = db_get_prepare_stmt($link, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Returns tag's ID
 * @param mysqli $link The MySQL connection.
 * @param string $tag The string contains tags
 * @param array $tags The array of exist tags
 *
 * @return bool|int false if ID is not exist or tag's ID
 */
function get_tag_id(mysqli $link, string $tag, array $tags)
{
    foreach ($tags as $item) {
        if ($item['tag'] === $tag) {
            return $item['id'];
        }
    }
    $sql = 'INSERT INTO tags (tag) VALUE (?)';
    $stmt = db_get_prepare_stmt($link, $sql, [$tag]);
    $result = mysqli_stmt_execute($stmt);
    return $result ? mysqli_insert_id($link) : false;
}

/**
 * Inserts a new rows post_tag into the database
 * @param mysqli $link The MySQL connection.
 * @param int $post_id Current post ID
 * @param array $tags_id Tags ID array for current post
 *
 * @return bool|string true if tags added or error massage
 */
function create_post_tag_sql(mysqli $link, int $post_id, array $tags_id)
{
    $request_values = [];
    foreach ($tags_id as $item) {
        $request_values[] = "({$post_id}, {$item})";
    }
    $request_string = implode(', ', $request_values);

    $sql = 'INSERT INTO post_tag (post_id, tag_id) VALUES'
        . $request_string;
    $result = mysqli_query($link, $sql);

    return $result ? true : ('Не удалось добавить новый тег' . mysqli_error($link));
}

/**
 * Return user's data by user_id
 * @param mysqli $link The MySQL connection
 * @param int $user_id
 *
 * @return array|null The user data array or null if user_id is not exist
 */
function get_user_data(mysqli $link,  int $user_id)
{
    $sql = 'SELECT users.*,
       (SELECT COUNT(post_id) FROM posts WHERE posts.user_id = users.id) as posts_count,
       (SELECT COUNT(subscriptions.id) FROM subscriptions WHERE subscriptions.author_id = users.id) as followers_count
        FROM users
        WHERE users.id = ?';
    $stmt = db_get_prepare_stmt($link, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_fetch_assoc($result);
}

/**
 * Return posts tags array, using post_id as keys
 * @param mysqli $link The MySQL connection
 * @param array $posts_id Array of the given posts id
 *
 * @return array an associative array of tags as
 * key is post's id and value is post's tags array
 */
function get_posts_tags(mysqli $link, array $posts_id)
{
    $posts_id_string = implode(', ', $posts_id);
    $sql = "SELECT post_tag.post_id, tags.tag
    FROM tags
    JOIN post_tag ON post_tag.tag_id = tags.id
    WHERE post_tag.post_id IN ({$posts_id_string})";
    $result = mysqli_query($link, $sql);
    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    $tags = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $post_tags = [];

    foreach ($tags as $key => $value) {
        $post_tags[$value['post_id']][] = $value['tag'];
    }
    return $post_tags;
}

/**
 * Adds a new post to the database and returns its ID
 * @param mysqli $link The MySQL connection
 * @param array $new_post Array of the post data
 *
 * @return int The post's ID
 */
function create_post_sql(mysqli $link, array $new_post)
{
    $sql = 'INSERT INTO posts (title, content, author_quote, img, video, link, view_count, user_id, post_type_id, original_id)
    VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $stmt = db_get_prepare_stmt($link, $sql, [
        $new_post['title'],
        $new_post ['content'],
        $new_post['author_quote'],
        $new_post['img'],
        $new_post['video'],
        $new_post['link'],
        $new_post['view_count'],
        $new_post['user_id'],
        $new_post['post_type_id'],
        $new_post['original_id']
    ]);

    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        exit ('error' . mysqli_error($link));
    }
    return mysqli_insert_id($link);
}

