<section class="profile__posts tabs__content tabs__content--active">
    <h2 class="visually-hidden">Публикации</h2>
    <?php if (!empty($posts)) : ?>
    <?php foreach ($posts as $key => $post) : ?>
            <article class="profile__post post post-<?= $post['class']?>">
                <header class="post__header">
<!-- for repost-->
                    <div class="post__author">
                        <a class="post__author-link" href="#" title="Автор">
                            <div class="post__avatar-wrapper post__avatar-wrapper--repost">
                                <img class="post__author-avatar" src="../img/userpic-tanya.jpg" alt="Аватар пользователя">
                            </div>
                            <div class="post__info">
                                <b class="post__author-name">Репост: Таня Фирсова</b>
                                <time class="post__time" datetime="2019-03-30T14:31">25 минут назад</time>
                            </div>
                        </a>
                    </div>
                </header>
                <div class="post__main">
                    <?= include_template('post-card-main.php', [
                        'post' => $post
                    ]) ?>
                </div>
                <footer class="post__footer">
                    <div class="post__indicators">
                        <div class="post__buttons">
                            <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                <svg class="post__indicator-icon" width="20" height="17">
                                    <use xlink:href="#icon-heart"></use>
                                </svg>
                                <svg class="post__indicator-icon post__indicator-icon--like-active" width="20" height="17">
                                    <use xlink:href="#icon-heart-active"></use>
                                </svg>
                                <span><?= $post['likes_count'] ?> </span>
                                <span class="visually-hidden">количество лайков</span>
                            </a>
                            <a class="post__indicator post__indicator--repost button" href="#" title="Репост">
                                <svg class="post__indicator-icon" width="19" height="17">
                                    <use xlink:href="#icon-repost"></use>
                                </svg>
                                <span>5</span>
                                <span class="visually-hidden">количество репостов</span>
                            </a>
                        </div>
                        <time class="post__time" datetime="2019-01-30T23:41">15 минут назад</time>
                    </div>
                    <ul class="post__tags">
                        <?php if (!empty($post['tags'])) : ?>
                            <?php foreach ($post['tags'] as $tag): ?>
                                <li><a href="/search.php?q=%23<?= $tag ?>">#<?= $tag ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </footer>
                <div class="comments">
                    <a class="comments__button button" href="#">Показать комментарии</a>
                </div>
            </article>
    <?php endforeach;?>
    <?php endif; ?>
</section>
