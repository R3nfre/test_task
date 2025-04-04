<?php
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $servicesCount array */

?>

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