<div class="container">
    <h1 class="page__title page__title--popular">Популярное</h1>
</div>
<div class="popular container">
    <div class="popular__filters-wrapper">
        <div class="popular__sorting sorting">
            <b class="popular__sorting-caption sorting__caption">Сортировка:</b>
            <ul class="popular__sorting-list sorting__list">
                <li class="sorting__item sorting__item--popular">
                    <a class="sorting__link <?= $sorting === 'view_count' ? 'sorting__link--active' : '' ?> <?= $order === 'ASC' ? 'sorting__link--reverse' : ''; ?>"
                       href="/popular.php?<?= get_query_string($_GET, [
                           'page' => 1,
                           'sorting' => 'view_count',
                           'order' => $sorting === 'view_count' && $order !== 'ASC' ? 'ASC' : 'DESC',
                       ]) ?>">
                        <span>Популярность</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <li class="sorting__item">
                    <a class="sorting__link <?= $sorting === 'likes_count' ? 'sorting__link--active' : '' ?> <?= $order === 'ASC' ? 'sorting__link--reverse' : ''; ?>"
                       href="/popular.php?<?= get_query_string($_GET, [
                           'page' => 1,
                           'sorting' => 'likes_count',
                           'order' => $sorting === 'likes_count' && $order !== 'ASC' ? 'ASC' : 'DESC',
                       ]) ?>">
                        <span>Лайки</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
                <li class="sorting__item">
                    <a class="sorting__link <?= $sorting === 'date' ? 'sorting__link--active' : '' ?> <?= $order === 'ASC' ? 'sorting__link--reverse' : ''; ?>"
                       href="/popular.php?<?= get_query_string($_GET, [
                           'page' => 1,
                           'sorting' => 'date',
                           'order' => $sorting === 'date' && $order !== 'ASC' ? 'ASC' : 'DESC',
                       ]) ?>">
                        <span>Дата</span>
                        <svg class="sorting__icon" width="10" height="12">
                            <use xlink:href="#icon-sort"></use>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
        <div class="popular__filters filters">
            <b class="popular__filters-caption filters__caption">Тип контента:</b>
            <ul class="popular__filters-list filters__list">
                <li class="popular__filters-item popular__filters-item--all filters__item filters__item--all">
                    <a class="filters__button filters__button--ellipse filters__button--all <?= empty($active_post_type) ? 'filters__button--active' : '' ?>"
                       href="/popular.php?<?= get_query_string($_GET, [
                           'page' => 1,
                           'sorting' => '',
                           'order' => '',
                           'post_type' => '',
                       ]) ?>">
                        <span>Все</span>
                    </a>
                </li>
                <?php foreach ($post_types as $post_type): ?>
                    <li class="popular__filters-item filters__item">
                        <a class="filters__button filters__button--<?= $post_type['class'] ?> <?= $active_post_type === $post_type['id'] ? 'filters__button--active' : '' ?> button"
                           href="/popular.php?<?= get_query_string($_GET, [
                               'page' => 1,
                               'sorting' => '',
                               'order' => '',
                               'post_type' => $post_type['id'],
                           ]) ?>">
                            <span class="visually-hidden"><?= $post_type['name'] ?></span>
                            <svg class="filters__icon" width="22" height="18">
                                <use xlink:href="#icon-filter-<?= $post_type['class'] ?>"></use>
                            </svg>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php if (!empty($popular_posts)) : ?>
        <div class="popular__posts">
            <?php foreach ($popular_posts as $key => $post): ?>
                <article class="popular__post post post-<?= $post['class']; ?>">
                    <header class="post__header">
                        <h2>
                            <a href="/post.php?post_id=<?= $post['post_id'] ?>">
                                <?= esc($post['title']); ?>
                            </a>
                        </h2>
                    </header>
                    <div class="post__main">
                        <?php switch ($post['class']):
                            case QUOTE: ?>
                                <blockquote>
                                    <p><?= esc($post['content']); ?></p>
                                    <cite><?= esc($post['author_quote']); ?></cite>
                                </blockquote>
                                <?php break; ?>
                            <?php case LINK: ?>
                                <div class="post-link__wrapper">
                                    <a class="post-link__external" href="<?= esc($post['link']) ?>"
                                       title="Перейти по ссылке">
                                        <div class="post-link__info-wrapper">
                                            <div class="post-link__icon-wrapper">
                                                <img
                                                        src="https://www.google.com/s2/favicons?domain=<?= esc($post['link']) ?>"
                                                        alt="Иконка">
                                            </div>
                                            <div class="post-link__info">
                                                <h3><?= esc($post['title']); ?></h3>
                                            </div>
                                        </div>
                                        <span><?= esc($post['link']); ?></span>
                                    </a>
                                </div>
                                <?php break; ?>
                            <?php case PHOTO: ?>
                                <div class="post-photo__image-wrapper">
                                    <img src="<?= esc($post['img']); ?>" alt="Фото от пользователя" width="360"
                                         height="240">
                                </div>
                                <?php break; ?>
                            <?php case VIDEO: ?>
                                <div class="post-video__block">
                                    <div class="post-video__preview">
                                        <?= embed_youtube_cover(esc($post['video'])); ?>
                                    </div>
                                    <a href="/post.php/?post_id=<?= $post['post_id'] ?>"
                                       class="post-video__play-big button">
                                        <svg class="post-video__play-big-icon" width="14" height="14">
                                            <use xlink:href="#icon-video-play-big"></use>
                                        </svg>
                                        <span class="visually-hidden">Запустить проигрыватель</span>
                                    </a>
                                </div>
                                <?php break; ?>
                            <?php case TEXT: ?>
                                <?php $post_content = cut_text(esc($post['content'])) ?>
                                <p><?= $post_content ?></p>
                                <?php if ($post_content !== esc($post['content'])) : ?>
                                    <a class="post-text__more-link" href="/post.php?post_id=<?= $post['post_id'] ?>">Читать
                                        далее</a>
                                <?php endif; ?>
                                <?php break; ?>
                            <?php endswitch; ?>
                    </div>
                    <footer class="post__footer">
                        <div class="post__author">
                            <a class="post__author-link" href="/profile.php?user_id=<?= $post['user_id'] ?>"
                               title="Автор">
                                <div class="post__avatar-wrapper">
                                    <img class="post__author-avatar" src="<?= $post['picture']; ?>"
                                         alt="Аватар пользователя">
                                </div>
                                <div class="post__info">
                                    <b class="post__author-name"><?= esc($post['login']); ?></b>
                                    <time class="post__time" title="<?= $post['date']; ?>"
                                          datetime="<?= $post['date'] ?>"><?= get_relative_date($post['date']); ?> назад
                                    </time>
                                </div>
                            </a>
                        </div>
                        <div class="post__indicators">
                            <div class="post__buttons">
                                <a class="post__indicator post__indicator--likes <?= $post['is_liked'] ? 'post__indicator--likes-active' : '' ?> button"
                                   href="/like.php?post_id=<?= $post['post_id'] ?>" title="Лайк">
                                    <svg class="post__indicator-icon <?= $post['is_liked'] ? 'post__indicator-icon--like-active' : '' ?>"
                                         width="20" height="17">
                                        <use xlink:href="<?= $post['is_liked'] ? '#icon-heart-active' : '#icon-heart' ?>"></use>
                                    </svg>
                                    <span><?= $post['likes_count'] ?></span>
                                    <span class="visually-hidden">количество лайков</span>
                                </a>
                                <a class="post__indicator post__indicator--comments button"
                                   href="/post.php?post_id=<?= $post['post_id'] ?>" title="Комментарии">
                                    <svg class="post__indicator-icon" width="19" height="17">
                                        <use xlink:href="#icon-comment"></use>
                                    </svg>
                                    <span><?= $post['comments_count'] ?></span>
                                    <span class="visually-hidden">количество комментариев</span>
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if ($pages_count > 1): ?>
            <div class="popular__page-links">
                <a class="popular__page-link popular__page-link--prev button button--gray"
                    <?php if ($prev_page): ?>
                        href="/popular.php?<?= get_query_string($_GET, ['page' => $prev_page]) ?>"
                    <?php endif; ?>
                >Предыдущая страница
                </a>
                <a class="popular__page-link popular__page-link--next button button--gray"
                    <?php if ($next_page): ?>
                        href="/popular.php?<?= get_query_string($_GET, ['page' => $next_page]) ?>"
                    <?php endif; ?>
                >Следующая страница
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

