<main class="page__main page__main--messages">
    <h1 class="visually-hidden">Личные сообщения</h1>
    <section class="messages tabs">
        <h2 class="visually-hidden">Сообщения</h2>
        <div class="messages__contacts">
            <?php if (!empty($contacts)): ?>
                <ul class="messages__contacts-list tabs__list">
                    <?php foreach ($contacts as $contact_id => $contact): ?>
                        <li class="messages__contacts-item">
                            <a class="messages__contacts-tab <?= $current_contact === $contact_id ? 'messages__contacts-tab--active tabs__item--active' : '' ?> tabs__item "
                               href="/messages.php?contact_id=<?= $contact_id ?>">
                                <div class="messages__avatar-wrapper">
                                    <img class="messages__avatar" src="<?= $contact['picture'] ?>"
                                         alt="Аватар пользователя">
                                </div>
                                <div class="messages__info">
                                    <span class="messages__contact-name"><?= $contact['name'] ?></span>
                                    <div class="messages__preview">
                                        <p class="messages__preview-text">
                                            <?= !empty($contact['last_message']) ? esc(cut_text($contact['last_message']['message'],
                                                20)) : '' ?>
                                        </p>
                                        <time class="messages__preview-time"
                                              datetime="<?= $contact['last_message']['date'] ?>">
                                            14:40
                                        </time>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="messages__chat">
            <div class="messages__chat-wrapper">
                <?php if (!empty($contacts_messages)): ?>
                    <?php foreach ($contacts_messages as $contact_id => $chat): ?>
                        <ul class="messages__list tabs__content <?= $current_contact === $contact_id ? 'tabs__content--active' : '' ?>">
                            <?php foreach ($chat as $key => $message): ?>
                                <li class="messages__item <?= $message['my_message'] ? 'messages__item--my' : '' ?>">
                                    <div class="messages__info-wrapper">
                                        <div class="messages__item-avatar">
                                            <a class="messages__author-link" href="#">
                                                <img class="messages__avatar" src="../img/userpic-larisa-small.jpg"
                                                     alt="Аватар пользователя">
                                            </a>
                                        </div>
                                        <div class="messages__item-info">
                                            <a class="messages__author" href="#">
                                                Лариса Роговая
                                            </a>
                                            <time class="messages__time" datetime="2019-05-01T14:40">
                                                1 ч назад
                                            </time>
                                        </div>
                                    </div>
                                    <p class="messages__text">
                                        <?= $message['message'] ?>
                                    </p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="comments">
                <form class="comments__form form" action="/messages.php" method="post">
                    <div class="comments__my-avatar">
                        <img class="comments__picture" src="" alt="Аватар пользователя">
                    </div>
                    <div class="form__input-section <?= !empty($errors) ? 'form__input-section--error' : '' ?> ">
                        <textarea class="comments__textarea form__textarea form__input"
                          placeholder="Ваше сообщение" name="new_message"></textarea>
                        <input type="hidden" name="recipient_id" value="<?= $current_contact ?? '' ?>">
                        <label class="visually-hidden">Ваше сообщение</label>
                        <button class="form__error-button button" type="button">!</button>
                        <?php if (!empty($errors)): ?>
                            <div class="form__error-text">
                                <h3 class="form__error-title">Ошибка валидации</h3>
                                <?php foreach ($errors as $error): ?>
                                    <p class="form__error-desc"><?= $error ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button class="comments__submit button button--green" type="submit">Отправить</button>
                </form>
            </div>
        </div>
    </section>
</main>
