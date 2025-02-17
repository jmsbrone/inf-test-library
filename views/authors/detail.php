<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var Author $author */

/** @var bool $enableEdit */

use app\models\Author;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="library-author-page">
    <h1><?= $author->id ? 'Editing' : 'Adding' ?> author</h1>
    <?= Html::a('Back', ['authors/list']) ?>
    <?php
    $action = $author->isNewRecord ? 'authors/create' : 'authors/update';
    $form = ActiveForm::begin([
        'id' => 'author-form',
        'action' => [$action, 'id' => $author->id],
    ]);
    ?>

    <?= $form->field($author, 'surname') ?>
    <?= $form->field($author, 'name') ?>
    <?= $form->field($author, 'last_name') ?>

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
