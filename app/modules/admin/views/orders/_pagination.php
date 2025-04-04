<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $params array */

use yii\widgets\LinkPager;
use yii\helpers\Url;
?>

<div class="row">
    <div class="col-sm-8">
        <nav>
            <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'prevPageLabel' => '&laquo;',
                'nextPageLabel' => '&raquo;',
                'firstPageLabel' => 1,
                'lastPageLabel' => $dataProvider->pagination->getPageCount(),
                'maxButtonCount' => 10,
                'options' => ['class' => 'pagination'],
                'linkOptions' => ['class' => 'page-link'],
                'disabledPageCssClass' => 'disabled',
                'activePageCssClass' => 'active',
            ]) ?>
        </nav>
    </div>

    <div class="col-sm-4 pagination-counters">
        <a href="<?= Url::to(array_merge(['/admin/orders/download-csv'], $params)) ?>" class="btn btn-primary pull-right">
            Save result
        </a>
        <?= Yii::t('order', 'page.pagination.text', [
            'begin' => $dataProvider->pagination->offset + 1,
            'end' => min(
                $dataProvider->pagination->offset + $dataProvider->pagination->limit,
                $dataProvider->pagination->totalCount
            ),
            'total' => $dataProvider->pagination->totalCount
        ]) ?>
    </div>
</div>