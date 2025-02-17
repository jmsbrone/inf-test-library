<?php

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1>Library</h1>
        <?= Html::a('Authors', Url::to(['authors/list'])) ?>
        <?= Html::a('Books', Url::to(['books/list'])) ?>
    </div>
</div>
