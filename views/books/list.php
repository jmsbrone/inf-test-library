<?php

/** @var yii\web\View $this */

/** @var Book[] $books */

use app\models\Book;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<h1>Books</h1>
<?= Html::a('Add', Url::to(['books/new'])) ?>

<?php foreach ($books as $book) : ?>
    <div class="row">
        <div class="col-lg-3">
            <img src="<?= $book->cover_img_path ?>" class="img-thumbnail">
            <div class="row">
                Title: <?= $book->title ?>
            </div>
            <div class="row">
                Authors: <?= join(', ', ArrayHelper::getColumn($book->authors, 'surname')) ?>
            </div>
        </div>
        <div class="col-lg-3">
            <?= Html::a('Edit', ['books/view', 'id' => $book->id]) ?>
            <form action="<?= Url::to(['books/delete', 'id' => $book->id]) ?>" method="post">
                <?= Html::submitButton('Delete') ?>
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>
            </form>
        </div>
    </div>
<?php endforeach; ?>
