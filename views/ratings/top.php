<?php

/** @var yii\web\View $this */
/** @var TopRatingRecord[] $topRecords */

use app\values\TopRatingRecord;

?>

<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="jumbotron">
                    <h1>Top 10 authors</h1>
                    <?php foreach ($topRecords as $record) { ?>
                        <div>
                            <?= $record->author->getFullName() ?> [<?= $record->year ?>] - <?= $record->book_count ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
