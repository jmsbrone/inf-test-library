<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var Author $author */

use app\models\Author;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="library-author-page">
    <h1><?= $author->id ? 'Editing' : 'Adding' ?> author</h1>
    <?= Html::a('Back', ['library/authors']) ?>
    <?php
    $form = ActiveForm::begin(['id' => 'author-form']);
    ?>

    <?= $form->field($author, 'surname') ?>
    <?= $form->field($author, 'name') ?>
    <?= $form->field($author, 'last_name') ?>

    <div class="form-group">
        <div>
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php
    ActiveForm::end();
    ?>

</div>
