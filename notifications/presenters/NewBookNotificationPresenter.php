<?php

namespace app\notifications\presenters;

use app\models\Book;

/**
 * Класс для формирования сообщений для уведомлений о выходе книги
 */
class NewBookNotificationPresenter
{
    /**
     * Получение текста сообщения для уведомления
     *
     * @param Book $book
     *
     * @return string
     */
    public function getMessageText(Book $book): string
    {
        $authorNames = [];
        foreach($book->authors as $author) {
            $authorNames[] = $author->getFullName();
        }
        $authorNamesText = implode(', ', $authorNames);

        return "Вышла новая книга \"{$book->title}\". Авторы: $authorNamesText";
    }
}
