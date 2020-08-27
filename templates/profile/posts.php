<section class="profile__posts tabs__content tabs__content--active">
    <h2 class="visually-hidden">Публикации</h2>
    <?php if (!empty($posts)) : ?>
    <?php foreach ($posts as $key => $post) : ?>
            <article class="profile__post post post-<?= $post['class']?>">
                <header class="post__header">
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
                    <?php switch ($post['class']): ?><?php case TEXT: ?>
                        <h2>
                            <a href="/post.php?post_id=<?= $post['post_id'] ?>"><?= esc($post['title']) ?></a>
                        </h2>
                        <?php $post_content = esc($post['content']) ?>
                        <p>
                            <?= cut_text($post_content) ?>
                        </p>
                        <?php if ($post_content !== cut_text($post_content)) : ?>
                            <a class="post-text__more-link"
                               href="/post.php?post_id=<?= $post['post_id'] ?>">Читать далее</a>
                        <?php endif; ?>
                        <?php break; ?>
                    <?php case VIDEO: ?>
                        <div class="post-video__block">
                            <div class="post-video__preview">
                                <?= embed_youtube_video(esc($post['video'])); ?>
                            </div>
                            <div class="post-video__control">
                                <button
                                    class="post-video__play post-video__play--paused button button--video"
                                    type="button"><span class="visually-hidden">Запустить видео</span>
                                </button>
                                <div class="post-video__scale-wrapper">
                                    <div class="post-video__scale">
                                        <div class="post-video__bar">
                                            <div class="post-video__toggle"></div>
                                        </div>
                                    </div>
                                </div>
                                <button
                                    class="post-video__fullscreen post-video__fullscreen--inactive button button--video"
                                    type="button"><span
                                        class="visually-hidden">Полноэкранный режим</span>
                                </button>
                            </div>
                            <button class="post-video__play-big button" type="button">
                                <svg class="post-video__play-big-icon" width="27" height="28">
                                    <use xlink:href="#icon-video-play-big"></use>
                                </svg>
                                <span class="visually-hidden">Запустить проигрыватель</span>
                            </button>
                        </div>
                        <?php break; ?>
                    <?php case QUOTE: ?>
                        <blockquote>
                            <p>
                                <?= esc($post['content']) ?>
                            </p>
                            <cite><?= esc($post['author_quote']) ?></cite>
                        </blockquote>
                        <?php break; ?>
                    <?php case LINK: ?>
                        <div class="post-link__wrapper">
                            <a class="post-link__external" href="<?= esc($post['link']) ?>"
                               title="Перейти по ссылке">
                                <div class="post-link__icon-wrapper">
                                    <img
                                        src="https://www.google.com/s2/favicons?domain=<?= esc($post['link']) ?>"
                                        alt="Иконка">
                                </div>
                                <div class="post-link__info">
                                    <h3><?= esc($post['title']) ?></h3>
                                    <p></p>
                                    <span><?= esc($post['link']) ?></span>
                                </div>
                                <svg class="post-link__arrow" width="11" height="16">
                                    <use xlink:href="#icon-arrow-right-ad"></use>
                                </svg>
                            </a>
                        </div>
                        <?php break; ?>
                    <?php case PHOTO: ?>
                        <h2><a href="#"><?= esc($post['title']) ?></a></h2>
                        <div class="post-photo__image-wrapper">
                            <img src="<?= esc($post['img']) ?>" alt="Фото от пользователя" width="760"
                                 height="396">
                        </div>
                    <?php endswitch ?>
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
                                <span>250</span>
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
