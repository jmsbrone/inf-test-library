<?php

namespace app\values;

use app\models\Author;

/**
 * Данные записи в рейтинге топа.
 */
readonly class TopRatingRecord
{
    public function __construct(
        public Author $author,
        public int $book_count,
        public int $year,
    ) {
    }
}
