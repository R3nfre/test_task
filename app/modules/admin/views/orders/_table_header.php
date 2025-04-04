<?php
/* @var $searchModel app\modules\admin\models\search\OrdersSearch */
/* @var $servicesCount array */
/* @var $totalCountWithoutSectionFilter int */

use app\modules\admin\models\Orders;
use app\modules\admin\widgets\orders\DropdownFilterWidget;

?>

<thead>
<tr>
    <th><?= Yii::t('order', 'table.header.id.name') ?> </th>
    <th><?= Yii::t('order', 'table.header.user.name') ?></th>
    <th><?= Yii::t('order', 'table.header.link.name') ?></th>
    <th><?= Yii::t('order', 'table.header.quantity.name') ?></th>
    <?= DropdownFilterWidget::widget([
        'searchModel' => $searchModel,
        'title' => Yii::t('order', 'table.header.service.name'),
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
    <th><?= Yii::t('order', 'table.header.status.name') ?></th>
    <?= DropdownFilterWidget::widget([
        'searchModel' => $searchModel,
        'title' => Yii::t('order', 'table.header.mode.name'),
        'items' => Orders::getModeNameList(),
        'currentValue' => Yii::$app->request->get('mode'),
        'paramName' => 'mode',
        'showCounts' => false,
        'totalCount' => null
    ]) ?>
    <th><?= Yii::t('order', 'table.header.created.name') ?></th>
</tr>
</thead>