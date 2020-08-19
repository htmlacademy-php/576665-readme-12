<main class="page__main page__main--search-results">
    <h1 class="visually-hidden">Страница результатов поиска<?= empty($posts) ? ': (нет результатов)' : '' ?></h1>
    <section class="search">
        <h2 class="visually-hidden">Результаты поиска</h2>
        <div class="search__query-wrapper">
            <div class="search__query container">
                <span>Вы искали:</span>
                <span class="search__query-text"><?= esc($search_query) ?></span>
            </div>
        </div>
        <div class="search__results-wrapper">
            <div class="container">
                <div class="search__content">
                    <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <article class="search__post post post-<?= $post['class'] ?>">
                            <header class="post__header post__author">
                                <a class="post__author-link" href="#" title="Автор">
                                    <div class="post__avatar-wrapper">
                                        <img class="post__author-avatar" src="<?= $post['picture'] ?>"
                                             alt="Аватар пользователя" width="60" height="60">
                                    </div>
                                    <div class="post__info">
                                        <b class="post__author-name"><?= $post['login'] ?></b>
                                        <span class="post__time"><?= relative_date($post['date']) ?></span>
                                    </div>
                                </a>
                            </header>
                            <div class="post__main">
                                <?php switch ($post['class']): ?><?php case TEXT: ?>
                                    <h2>
                                        <a href="/post.php?post_id=<?= $post['post_id'] ?>"><?= esc($post['title']) ?></a>
                                    </h2>
                                    <?php $post_content = $post['content'] ?>
                                    <p>
                                        <?= cut_text($post_content) ?>
                                    </p>
                                    <?php if ($post_content !== esc($post['content'])) : ?>
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
                                                type="button"><span class="visually-hidden">Полноэкранный режим</span>
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
                            <footer class="post__footer post__indicators">
                                <div class="post__buttons">
                                    <a class="post__indicator post__indicator--likes button" href="#" title="Лайк">
                                        <svg class="post__indicator-icon" width="20" height="17">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                        <svg class="post__indicator-icon post__indicator-icon--like-active" width="20"
                                             height="17">
                                            <use xlink:href="#icon-heart-active"></use>
                                        </svg>
                                        <span>250</span>
                                        <span class="visually-hidden">количество лайков</span>
                                    </a>
                                    <a class="post__indicator post__indicator--comments button" href="#"
                                       title="Комментарии">
                                        <svg class="post__indicator-icon" width="19" height="17">
                                            <use xlink:href="#icon-comment"></use>
                                        </svg>
                                        <span>25</span>
                                        <span class="visually-hidden">количество комментариев</span>
                                    </a>
                                </div>
                                <div>
                                    <ul class="post__tags">
                                        <?php foreach ($post_tags as $tag): ?>
                                            <li><a href="/search.php?q=<?= $tag ?>">#<?= $tag ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                            </footer>
                        </article>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <div class="search__results-wrapper">
                            <div class="search__no-results container">
                                <p class="search__no-results-info">К сожалению, ничего не найдено.</p>
                                <p class="search__no-results-desc">
                                    Попробуйте изменить поисковый запрос или просто зайти в раздел &laquo;Популярное&raquo;, там живет самый крутой контент.
                                </p>
                                <div class="search__links">
                                    <a class="search__popular-link button button--main" href="#">Популярное</a>
                                    <a class="search__back-link" href="#">Вернуться назад</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>
