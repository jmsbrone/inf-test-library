<?php

namespace app\controllers;

use app\models\Author;
use app\values\TopRatingRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class RatingsController extends Controller
{
    public const TOP_COUNT = 10;

    public function actionTop()
    {
        $query =
            (new Query())
                ->from('author_book')
                ->select([
                    'author_book.author_id',
                    'COUNT(books.id) as book_count',
                    'books.year',
                ])
                ->innerJoin('books', 'author_book.book_id=books.id')
                ->groupBy(['books.year', 'author_book.author_id'])
                ->orderBy('book_count DESC')
                ->limit(self::TOP_COUNT);

        $topRecordsData = $query->all();
        $authorIds = ArrayHelper::getColumn($topRecordsData, 'author_id');
        $authors = ArrayHelper::index(Author::findAll(['id' => $authorIds]), 'id');

        $topRecords = [];
        foreach ($topRecordsData as $record) {
            $topRecords[] = new TopRatingRecord($authors[$record['author_id']], $record['book_count'], $record['year']);
        }

        return $this->render('top', ['topRecords' => $topRecords]);
    }
}
