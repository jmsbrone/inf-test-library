<?php

/** @var yii\web\View $this */
/** @var Book $book */

/** @var bool $enableEdit */

use app\forms\EditBookForm;
use app\models\Author;
use app\models\Book;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="library-book-page">
    <h1><?= $book->id ? 'Editing' : 'Adding' ?> book</h1>
    <?= Html::a('Back', ['books/list']) ?>
    <?php
    $action = $book->isNewRecord ? 'books/create' : 'books/update';

    $editBookForm = new EditBookForm();
    $editBookForm->fillFromBook($book);

    $form = ActiveForm::begin([
        'id' => 'book-form',
        'action' => [$action, 'id' => $book->id],
    ]);
    ?>

    <?= $form->field($editBookForm, 'title') ?>
    <?= $form->field($editBookForm, 'year') ?>
    <?= $form->field($editBookForm, 'description') ?>
    <?= $form->field($editBookForm, 'isbn') ?>
    <?= $form->field($editBookForm, 'authors')->listBox(
        ArrayHelper::map(Author::find()->all(), 'id', 'name'),
        ['multiple' => true],
    ) ?>
    <?= $form->field($editBookForm, 'cover_img')->fileInput() ?>
    <?php
    if (!empty($editBookForm->cover_img_path)) { ?>
        <img src="<?= $editBookForm->cover_img_path ?>">
        <?php
    } ?>

    <?php
    if ($enableEdit) { ?>
        <div class="form-group">
            <div>
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php
    }
    ActiveForm::end();
    ?>

</div>
