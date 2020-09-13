<h2 class="visually-hidden">Лайки</h2>
<?php if (!empty($posts_likes)): ?>
    <ul class="profile__likes-list">
        <?php foreach ($posts_likes as $like): ?>
            <li class="post-mini post-mini--<?= $like['class'] ?> post user">
                <div class="post-mini__user-info user__info">
                    <div class="post-mini__avatar user__avatar">
                        <a class="user__avatar-link" href="/profile.php?user_id=<?= $like['user_id'] ?>">
                            <img class="post-mini__picture user__picture" src="/<?= $like['picture'] ?>"
                                 alt="Аватар пользователя">
                        </a>
                    </div>
                    <div class="post-mini__name-wrapper user__name-wrapper">
                        <a class="post-mini__name user__name" href="/profile.php?user_id=<?= $like['user_id'] ?>">
                            <span><?= $like['login'] ?></span>
                        </a>
                        <div class="post-mini__action">
                            <span class="post-mini__activity user__additional">Лайкнул вашу публикацию</span>
                            <time class="post-mini__time user__additional"
                                  datetime="<?= $like['date'] ?>"><?= get_relative_date($like['date']) ?> назад
                            </time>
                        </div>
                    </div>
                </div>
                <div class="post-mini__preview">
                    <a class="post-mini__link" href="/post.php?post_id=<?= $like['post_id'] ?>"
                       title="Перейти на публикацию">
                        <?php switch ($like['class']):
                            case PHOTO: ?>
                                <div class="post-mini__image-wrapper">
                                    <img class="post-mini__image" src="<?= $like['img'] ?>" width="109" height="109"
                                         alt="Превью публикации">
                                </div>
                                <span class="visually-hidden">Фото</span>
                                <?php break; ?>
                            <?php case TEXT: ?>
                                <span class="visually-hidden">Текст</span>
                                <svg class="post-mini__preview-icon" width="20" height="21">
                                    <use xlink:href="#icon-filter-text"></use>
                                </svg>
                                <?php break; ?>
                            <?php case VIDEO: ?>
                                <div class="post-mini__image-wrapper">
                                    <?= embed_youtube_cover(esc($like['video'])); ?>
                                    <span class="post-mini__play-big">
                                        <svg class="post-mini__play-big-icon" width="12" height="13">
                                            <use xlink:href="#icon-video-play-big"></use>
                                        </svg>
                                    </span>
                                </div>
                                <?php break; ?>
                            <?php case QUOTE: ?>
                                <span class="visually-hidden">Цитата</span>
                                <svg class="post-mini__preview-icon" width="21" height="20">
                                    <use xlink:href="#icon-filter-quote"></use>
                                </svg>
                                <?php break; ?>
                            <?php case LINK: ?>
                                <span class="visually-hidden">Ссылка</span>
                                <svg class="post-mini__preview-icon" width="21" height="18">
                                    <use xlink:href="#icon-filter-link"></use>
                                </svg>
                                <?php break; ?>
                            <?php endswitch; ?>
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

