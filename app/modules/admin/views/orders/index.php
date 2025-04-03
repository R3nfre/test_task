<?php
/* @var $params array */
/* @var $searchModel app\modules\admin\models\order\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $servicesCount array */
/* @var $totalCountWithoutSectionFilter int */

use app\modules\admin\models\order\Order;
use app\modules\admin\widgets\orders\DropdownFilterWidget;
use app\modules\admin\widgets\orders\SearchWidget;
use app\modules\admin\widgets\orders\StatusWidget;
use yii\widgets\LinkPager;

$currentStatus = Yii::$app->request->get('status');
$params = Yii::$app->request->getQueryParams();
?>

<ul class="nav nav-tabs p-b">
    <?= StatusWidget::widget(['params' => $params]); ?>
    <?= SearchWidget::widget(['params' => $params]); ?>
</ul>

<table class="table order-table">
    <thead>
    <tr>
        <th><?= Yii::t('order', 'id_name') ?> </th>
        <th><?= Yii::t('order', 'user_name') ?></th>
        <th><?= Yii::t('order', 'link_name') ?></th>
        <th><?= Yii::t('order', 'quantity_name') ?></th>
        <?= DropdownFilterWidget::widget([
            'params' => $params,
            'title' => Yii::t('order', 'service_name'),
            'items' => array_map(function ($item) {
                return [
                    'name' => $item['service_name'],
                    'count' => $item['service_count']
                ];
            }, $servicesCount),
            'currentValue' => Yii::$app->request->get('service_id'),
            'paramName' => 'service_id',
            'showCounts' => true,
            'totalCount' => $totalCountWithoutSectionFilter
        ]) ?>
        <th><?= Yii::t('order', 'status_name') ?></th>
        <?= DropdownFilterWidget::widget([
            'params' => $params,
            'title' => Yii::t('order', 'mode_name'),
            'items' => Order::getModeList(),
            'currentValue' => Yii::$app->request->get('mode'),
            'paramName' => 'mode',
            'showCounts' => false,
            'totalCount' => null
        ]) ?>
        <th><?= Yii::t('order', 'created_name') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($dataProvider->models as $order) { ?>
        <tr>
            <td><?= $order->id ?></td>
            <td><?= $order->user->first_name . ' ' . $order->user->last_name ?></td>
            <td class="link"><?= $order->link ?></td>
            <td><?= $order->quantity ?></td>
            <td class="service">
                <span class="label-id"><?= $servicesCount[$order->service_id]['service_count'] ?? 0 ?></span>
                <?= ' ' . $order->service->name ?>
            </td>
            <td><?= $order->getStatusName() ?></td>
            <td><?= $order->getModeName() ?></td>
            <td>
                <span class="nowrap"><?= Yii::$app->formatter->asDate($order->created_at, 'php:Y-m-d') ?></span>
                <span class="nowrap"><?= Yii::$app->formatter->asTime($order->created_at, 'php:H:i:s') ?></span>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

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
        <? $params = Yii::$app->request->getQueryParams(); ?>
        <a href="<?= \yii\helpers\Url::to(array_merge(['/admin/orders/download-csv'], $params)) ?>" class="btn btn-primary pull-right">
            Save result
        </a>
        <?= Yii::t('order', 'pagination_text', [
            'begin' => $dataProvider->pagination->offset + 1,
            'end' => min(
                    $dataProvider->pagination->offset + $dataProvider->pagination->limit,
                    $dataProvider->pagination->totalCount
            ),
            'total' => $dataProvider->pagination->totalCount
        ]) ?>

    </div>
</div>
