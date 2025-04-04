<?php
/* @var $searchModel app\modules\admin\models\search\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $servicesCount array */
/* @var $totalCountWithoutSectionFilter int */

use app\modules\admin\widgets\orders\SearchWidget;
use app\modules\admin\widgets\orders\StatusWidget;

$currentStatus = Yii::$app->request->get('status');
$params = Yii::$app->request->getQueryParams();
?>

<ul class="nav nav-tabs p-b">
    <?= StatusWidget::widget([
            'params' => $searchModel->getFilteredAttributes(),
            'searchModel' => $searchModel,
    ]); ?>
    <?= SearchWidget::widget(['searchModel' => $searchModel]); ?>
</ul>

<table class="table order-table">
    <?= $this->render('_table_header', [
        'searchModel' => $searchModel,
        'servicesCount' => $servicesCount,
        'totalCountWithoutSectionFilter' => $totalCountWithoutSectionFilter,
    ]) ?>
    <?= $this->render('_table_body', [
        'dataProvider' => $dataProvider,
        'servicesCount' => $servicesCount,
        'params' => $params,
    ]) ?>
</table>

<?= $this->render('_pagination', [
    'dataProvider' => $dataProvider,
    'params' => $params,
]) ?>
