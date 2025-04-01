<?php

use app\modules\admin\models\Order;
use yii\helpers\Url;
use yii\widgets\LinkPager;

?>

    <ul class="nav nav-tabs p-b">
        <?php
        $currentStatus = Yii::$app->request->get('status');
        $params = Yii::$app->request->getQueryParams();
        unset($params['service_id']);
        unset($params['mode']);
        unset($params['status']);
        ?>
        <li class="<?= $currentStatus === null ? 'active' : '' ?>">
            <a href="<?= Url::to(array_merge(['/admin/orders'], $params)) ?>"><?= Yii::t('order', 'all') ?></a>
        </li>
        <?php foreach (Order::getStatusList() as $orderStatusId => $orderStatusName) {
            $isActive = ($currentStatus !== null && $currentStatus == $orderStatusId);
            $params = Yii::$app->request->getQueryParams();
            $params['status'] = $orderStatusId;
            unset($params['mode']);
            unset($params['service_id']);
            ?>
            <li class="<?= $isActive ? 'active' : '' ?>"><a href="<?=  Url::to(array_merge(['/admin/orders'], $params)) ?>"><?= $orderStatusName ?></a></li>
        <?php } ?>
        <li class="pull-right custom-search">
            <form class="form-inline" action="/admin/orders" method="get">
                <div class="input-group">
                    <?php if($currentStatus){ ?>
                        <input type="hidden" name="status" value="<?= $currentStatus ?>">
                    <?php } ?>
                    <?php $value = Yii::$app->request->get('search') ?? '' ?>
                    <input type="text" name="search" class="form-control" value="<?= $value ?>" placeholder="<?= Yii::t('order', 'search_orders_name')?>">
                    <span class="input-group-btn search-select-wrap">

            <select class="form-control search-select" name="search-type">
              <option value="id" selected=""><?= Yii::t('order', 'id_order_name')?></option>
              <option value="link"><?= Yii::t('order', 'link_name')?></option>
              <option value="name"><?= Yii::t('order', 'username_name')?></option>
            </select>
            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
            </span>
                </div>
            </form>
        </li>
    </ul>
    <table class="table order-table">
        <thead>
        <tr>
            <th><?= Yii::t('order', 'id_name')?> </th>
            <th><?= Yii::t('order', 'user_name')?></th>
            <th><?= Yii::t('order', 'link_name')?></th>
            <th><?= Yii::t('order', 'quantity_name')?></th>
            <th class="dropdown-th">
                <div class="dropdown">
                    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <?= Yii::t('order', 'service_name')?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <?php
                        $currentService = Yii::$app->request->get('service_id');
                        $params = Yii::$app->request->getQueryParams();
                        unset($params['service_id']);
                        ?>
                        <li class="<?= $currentService === null ? 'active' : '' ?>">
                            <a href="<?=Url::to(array_merge(['/admin/orders'], $params))?>"><?= Yii::t('order', 'all').' ' ?> (<?=$totalCountWithoutSectionFilter?>)</a>
                        </li>
                        <?php foreach ($servicesCount as $serviceId => $serviceData){ ?>
                            <?php
                            $params = Yii::$app->request->getQueryParams();
                            $params['service_id'] = $serviceId;
                            $isActive = ($currentService !== null && $currentService == $serviceId);
                            $class = $serviceData['service_count'] === 0 ? 'disabled' : '';
                            $class .= $isActive ? ' active' : '';
                            $url = $serviceData['service_count'] === 0
                                ? 'javascript:void(0);'
                                : Url::to(array_merge(['/admin/orders'], $params));
                            ?>
                            <li class="<?= $class ?>">
                                <a href="<?= $url ?>">
                                    <span class="label-id"><?= $serviceData['service_count'] ?></span> <?= $serviceData['service_name'] ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </th>
            <th><?= Yii::t('order', 'status_name') ?></th>
            <th class="dropdown-th">
                <div class="dropdown">
                    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <?= Yii::t('order', 'mode_name') ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <?php
                        $currentMode = Yii::$app->request->get('mode');
                        $params = Yii::$app->request->getQueryParams();
                        unset($params['mode']);
                        ?>
                        <li class="<?= $currentMode === null ? 'active' : '' ?>">
                            <a href="<?= Url::to(array_merge(['/admin/orders'], $params)) ?>"><?= Yii::t('order', 'all') ?></a>
                        </li>
                        <?php foreach (Order::getModeList() as $orderModeId => $orderModeName){ ?>
                            <?php
                            $params = Yii::$app->request->getQueryParams();
                            $params['mode'] = $orderModeId;
                            $isActive = ($currentMode !== null && $currentMode == $orderModeId);
                            ?>
                            <li class="<?= $isActive ? 'active' : '' ?>"><a href="<?=  Url::to(array_merge(['/admin/orders'], $params)) ?>"><?= $orderModeName ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </th>
            <th><?= Yii::t('order', 'created_name')?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order){?>
        <tr>
            <td><?= $order->id ?></td>
            <td><?= $order->user->first_name . ' ' . $order->user->last_name?></td>
            <td class="link"><?= $order->link ?></td>
            <td><?= $order->quantity ?></td>
            <td class="service">
                <span class="label-id"><?= $servicesCount[$order->service_id]['service_count']?></span><?= ' '.$order->service->name ?>
            </td>
            <td><?= $order->getStatusName() ?></td>
            <td><?= $order->getModeName() ?></td>
            <td>
                <span class="nowrap"><?= Yii::$app->formatter->asDate($order->created_at, 'php:Y-m-d')?></span>
                <span class="nowrap"><?= Yii::$app->formatter->asTime($order->created_at, 'php:H:i:s')?></span>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
<div class="row">
    <div class="col-sm-8">
        <nav>
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'prevPageLabel' => '&laquo;',
                'nextPageLabel' => '&raquo;',
                'firstPageLabel' => 1,
                'lastPageLabel' => $pagination->getPageCount(),
                'maxButtonCount' => 10,
                'options' => ['class' => 'pagination'],
                'linkOptions' => ['class' => 'page-link'],
                'disabledPageCssClass' => 'disabled',
                'activePageCssClass' => 'active',
            ]) ?>
        </nav>
    </div>

    <div class="col-sm-4 pagination-counters">
        <?= Yii::t('order', 'pagination_text', [
            'begin' => $pagination->offset + 1,
            'end' => min($pagination->offset + $pagination->limit, $pagination->totalCount),
            'total' => $pagination->totalCount
        ]) ?>
    </div>
</div>
