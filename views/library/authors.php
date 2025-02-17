<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\Author[] $authors */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<h1>Authors</h1>
<?= Html::a('Add author', Url::to(['library/author'])) ?>

<?php
foreach ($authors as $author): ?>
    <div class="row">
        <div class="col-lg-3">
            <?= $author->surname . ' ' . $author->name . ' ' . $author->last_name ?>
        </div>
        <div class="col-lg-3">
            <?= Html::a('Edit', ['library/author', 'id' => $author->id]) ?>
            <form action="<?= Url::to(['library/delete-author', 'id' => $author->id]) ?>" method="post">
                <?= Html::submitButton('Delete') ?>
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>
            </form>
        </div>
    </div>
<?php
endforeach; ?>

