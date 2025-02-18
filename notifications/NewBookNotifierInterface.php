<?php

namespace app\notifications;

use app\models\Book;
use app\models\Subscription;

/**
 * Интерфейс для уведомления о новой книге.
 */
interface NewBookNotifierInterface
{
    /**
     * Отправка уведомления пользователю по подписке
     *
     * @param Book $book
     * @param Subscription $authorSubscription
     *
     * @return void
     */
    public function notify(Book $book, Subscription $authorSubscription): void;
}
