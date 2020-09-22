<main class="page__main page__main--publication">
    <div class="container">
        <h1 class="page__title page__title--publication"><?= esc($post['title']) ?></h1>
        <section class="post-details">
            <h2 class="visually-hidden">Публикация</h2>
            <div class="post-details__wrapper post-photo">
                <div class="post-details__main-block post post--details">
                    <div class="post-details__image-wrapper post-photo__image-wrapper">
                        <?= $post_content ?>
                    </div>
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes <?= $post['is_liked'] ? 'post__indicator--likes-active' : '' ?> button" href="/like.php?post_id=<?= $post['post_id'] ?>" title="Лайк">
                                <svg class="post__indicator-icon <?= $post['is_liked'] ? 'post__indicator-icon--like-active' : '' ?>" width="20" height="17">
                                    <use xlink:href="<?= $post['is_liked'] ? '#icon-heart-active' : '#icon-heart' ?>"></use>
                                </svg>
                                <span><?= $post['likes_count'] ?></span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--comments button" href="#last_comment" title="Комментарии">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-comment"></use>
                                </svg>
                                <span><?= $post['comments_count'] ?></span>
                                <span class="visually-hidden">количество комментариев</span>
                            </a>
                            <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-repost"></use>
                                </svg>
                                <span>5</span>
                                <span class="visually-hidden">количество репостов</span>
                            </a>
                        </div>
                        <span class="post__view">
                            <?= $post['view_count']; ?>
                            <?= get_noun_plural_form($post['view_count'], 'просмотр', 'просмотра', 'просмотров'); ?>
                        </span>
                    </div>
                    <div class="comments">
                        <form class="comments__form form" method="post">
                            <div class="comments__my-avatar">
                                <img class="comments__picture" src="<?= esc($current_user['picture']) ?>" alt="Аватар пользователя">
                            </div>
                            <div class="form__input-section <?= (!empty($errors)) ? 'form__input-section--error' : '' ?>">
                                <textarea class="comments__textarea form__textarea form__input"
                                          placeholder="Ваш комментарий" id="comment" name="comment"><?= !empty($new_comment['comment']) ? esc($new_comment['comment']) : '' ?></textarea>
                                <label class="visually-hidden" for="comment">Ваш комментарий</label>
                                <input type="hidden" name="post_id" value="<?= $post['post_id']?>">
                                <button class="form__error-button button" type="button">!</button>
                                <?php if (!empty($errors)): ?>
                                <div class="form__error-text">
                                    <h3 class="form__error-title">Ошибка валидации</h3>
                                    <p class="form__error-desc"><?= $errors['comment'] ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <button class="comments__submit button button--green" type="submit">Отправить</button>
                        </form>
                        <?php if (!empty($comments)): ?>
                        <div class="comments__list-wrapper">
                            <ul class="comments__list">
                                <?php foreach ($comments as $key => $comment): ?>
                                <li class="comments__item user" <?=$key === 0 ? 'id="last_comment"' : '' ?>>
                                    <div class="comments__avatar">
                                        <a class="user__avatar-link" href="profile.php?user_id=<?= $comment['user_id']?>">
                                            <img class="comments__picture" src="<?= $comment['picture'] ?>"
                                                 alt="Аватар пользователя">
                                        </a>
                                    </div>
                                    <div class="comments__info">
                                        <div class="comments__name-wrapper">
                                            <a class="comments__user-name" href="profile.php?user_id=<?= $comment['user_id']?>">
                                                <span><?= $comment['login'] ?></span>
                                            </a>
                                            <time class="comments__time" datetime="<?= $comment['date'] ?>"><?= get_relative_date($comment['date']) ?></time>
                                        </div>
                                        <p class="comments__text">
                                            <?= esc($comment['content']) ?>
                                        </p>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <a class="comments__more-link" href="#">
                                <span>Показать все комментарии</span>
                                <sup class="comments__amount"><?= $post['comments_count'] ?></sup>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="post-details__user user">
                    <div class="post-details__user-info user__info">
                        <div class="post-details__avatar user__avatar">
                            <a class="post-details__avatar-link user__avatar-link" href="profile.php?user_id=<?= $author_data['id']?>">
                                <img class="post-details__picture user__picture" src="/<?= $author_data['picture'] ?>"
                                     alt="Аватар пользователя">
                            </a>
                        </div>
                        <div class="post-details__name-wrapper user__name-wrapper">
                            <a class="post-details__name user__name" href="/profile.php?user_id=<?= $author_data['id']?>">
                                <span><?= $author_data['login'] ?></span>
                            </a>
                            <time class="post-details__time user__time" datetime="$user_data['registered']"><?= get_relative_date($author_data['registered'])?> назад</time>
                        </div>
                    </div>
                    <div class="post-details__rating user__rating">
                        <p class="post-details__rating-item user__rating-item user__rating-item--subscribers">
                            <span class="post-details__rating-amount user__rating-amount">
                                <?= $author_data['followers_count'] ?>
                            </span>
                            <span class="post-details__rating-text user__rating-text">
                                <?= get_noun_plural_form($author_data['followers_count'], 'подписчик', 'подписчика',
                                    'подписчиков'); ?>
                            </span>
                        </p>
                        <p class="post-details__rating-item user__rating-item user__rating-item--publications">
                            <span class="post-details__rating-amount user__rating-amount">
                                <?= $author_data['posts_count'] ?>
                            </span>
                            <span class="post-details__rating-text user__rating-text">
                                <?= get_noun_plural_form($author_data['posts_count'], 'публикация', 'публикации', 'публикаций'); ?>
                            </span>
                        </p>
                    </div>
                    <?php if ($author_data['id'] !== $current_user['id']): ?>
                        <div class="post-details__user-buttons user__buttons">
                            <a class="user__button user__button--subscription button <?= $author_data['is_following'] ? 'button--quartz' : 'button--main' ?>"
                               href="/subscription.php?author_id=<?= $author_data['id'] ?>">
                                <?= $author_data['is_following'] ? 'Отписаться' : 'Подписаться' ?>
                            </a>
                            <a class="user__button user__button--writing button button--green" href="#">Сообщение</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</main>
