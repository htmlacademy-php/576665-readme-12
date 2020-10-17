<main class="page__main page__main--adding-post">
    <div class="page__main-section">
        <div class="container">
            <h1 class="page__title page__title--adding-post">Добавить публикацию</h1>
        </div>
        <div class="adding-post container">
            <div class="adding-post__tabs-wrapper tabs">
                <div class="adding-post__tabs filters">
                    <ul class="adding-post__tabs-list filters__list tabs__list">
                        <?php foreach ($post_types as $post_type): ?>
                            <li class="adding-post__tabs-item filters__item">
                                <a class="adding-post__tabs-link filters__button filters__button--<?= $post_type['class'] ?> tabs__item <?= $active_post_type_id === (int)$post_type['id'] ? 'filters__button--active tabs__item--active' : '' ?> button"
                                   href="/add.php?post_type=<?= $post_type['id'] ?>">
                                    <svg class="filters__icon" width="22" height="18">
                                        <use xlink:href="#icon-filter-<?= $post_type['class'] ?>"></use>
                                    </svg>
                                    <span><?= $post_type['name'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="adding-post__tab-content">
                    <section class="adding-post__quote tabs__content tabs__content--active">
                        <h2 class="visually-hidden">Форма добавления</h2>
                        <form class="adding-post__form form" action="/add.php?post_type=<?= $active_post_type_id ?>"
                              method="post" enctype="multipart/form-data">
                            <div class="form__text-inputs-wrapper">
                                <div class="form__text-inputs">
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label"
                                               for="<?= $active_post_type ?>-heading">Заголовок <span
                                                    class="form__input-required">*</span></label>
                                        <div class="form__input-section <?= !empty($errors['title']) ? 'form__input-section--error' : '' ?>">
                                            <input class="adding-post__input form__input"
                                                   id="<?= $active_post_type ?>-heading" type="text" name="title"
                                                   placeholder="Введите заголовок"
                                                   value="<?= !empty($new_post['title']) ? esc($new_post['title']) : '' ?>">
                                            <button class="form__error-button button" type="button">!<span
                                                        class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Заголовок</h3>
                                                <p class="form__error-desc"><?= !empty($errors['title']) ? $errors['title'] : '' ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="post_type_id" value="<?= $active_post_type_id ?>">
                                    <?= $adding_post_content ?>
                                    <div class="adding-post__input-wrapper form__input-wrapper">
                                        <label class="adding-post__label form__label" for="cite-tags">Теги</label>
                                        <div class="form__input-section <?= !empty($errors['tags']) ? 'form__input-section--error' : '' ?>">
                                            <input class="adding-post__input form__input" id="cite-tags" type="text"
                                                   name="tags" placeholder="Введите теги"
                                                   value="<?= !empty($new_post['tags']) ? esc($new_post['tags']) : '' ?>">
                                            <button class="form__error-button button" type="button">!<span
                                                        class="visually-hidden">Информация об ошибке</span></button>
                                            <div class="form__error-text">
                                                <h3 class="form__error-title">Теги</h3>
                                                <p class="form__error-desc"><?= !empty($errors['tags']) ? $errors['tags'] : '' ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($errors)) : ?>
                                    <div class="form__invalid-block">
                                        <b class="form__invalid-slogan">Пожалуйста, исправьте следующие ошибки:</b>
                                        <ul class="form__invalid-list">
                                            <?php foreach ($errors as $key => $value) : ?>
                                                <li class="form__invalid-item"><?= !empty($error_titles[$key]) ? "{$error_titles[$key]}. " : '' ?><?= $value ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="adding-post__buttons">
                                <button class="adding-post__submit button button--main" type="submit">Опубликовать
                                </button>
                                <a class="adding-post__close" href="#">Закрыть</a>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</main>

