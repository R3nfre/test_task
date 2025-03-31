<?php

use app\modules\admin\models\Order;
use yii\helpers\Url;

$this->params = Yii::$app->request->getQueryParams();
?>

    <ul class="nav nav-tabs p-b">
        <?php $currentStatus = Yii::$app->request->get('status');?>
        <li class="<?= $currentStatus === null ? 'active' : '' ?>">
            <a href="<?= Url::to(['/admin/orders']) ?>"><?= Yii::t('order', 'all') ?></a>
        </li>
        <?php foreach (Order::getStatusList() as $orderStatusId => $orderStatusName) {
            $isActive = ($currentStatus !== null && $currentStatus == $orderStatusId);
            $this->params['status'] = $orderStatusId;
            ?>
            <li class="<?= $isActive ? 'active' : '' ?>"><a href="<?= Url::to(array_merge(['/admin/orders'], $this->params)) ?>"><?= $orderStatusName ?></a></li>
        <?php } ?>
        <li class="pull-right custom-search">
            <form class="form-inline" action="/admin/orders" method="get">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" value="" placeholder="Search orders">
                    <span class="input-group-btn search-select-wrap">

            <select class="form-control search-select" name="search-type">
              <option value="1" selected="">Order ID</option>
              <option value="2">Link</option>
              <option value="3">Username</option>
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
            <th>ID</th>
            <th>User</th>
            <th>Link</th>
            <th>Quantity</th>
            <th class="dropdown-th">
                <div class="dropdown">
                    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Service
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li class="active"><a href="">All (<?=$pagination->totalCount?>)</a></li>
                        <?php foreach ($servicesCount as $serviceName => $serviceCount){ ?>
                            <?php
                            $class = $serviceCount === 0 ? 'disabled' : '';
                            $url = $serviceCount === 0 ? 'javascript:void(0);' : '#';
                            ?>
                            <li class="<?= $class ?>">
                                <a href="<?= $url ?>">
                                    <span class="label-id"><?= $serviceCount ?></span> <?= $serviceName ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </th>
            <th>Status</th>
            <th class="dropdown-th">
                <div class="dropdown">
                    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Mode
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li class="active"><a href="">All</a></li>
                        <li><a href="">Manual</a></li>
                        <li><a href="">Auto</a></li>
                    </ul>
                </div>
            </th>
            <th>Created</th>
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
                <span class="label-id"><?= $servicesCount[$order->service->name]?></span><?= ' '.$order->service->name ?>
            </td>
            <td><?= $order->getStatusName() ?></td>
            <td><?= $order->mode ?></td>
            <td><span class="nowrap">2016-01-27</span><span class="nowrap">15:13:52</span></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <div class="row">
        <div class="col-sm-8">
            <nav>
                <ul class="pagination">
                    <li class="disabled"><a href="" aria-label="Previous">&laquo;</a></li>
                    <li class="active"><a href="">1</a></li>
                    <li><a href="">2</a></li>
                    <li><a href="">3</a></li>
                    <li><a href="">4</a></li>
                    <li><a href="">5</a></li>
                    <li><a href="">6</a></li>
                    <li><a href="">7</a></li>
                    <li><a href="">8</a></li>
                    <li><a href="">9</a></li>
                    <li><a href="">10</a></li>
                    <li><a href="" aria-label="Next">&raquo;</a></li>
                </ul>
            </nav>

        </div>
        <div class="col-sm-4 pagination-counters">
            1 to 100 of <?= $pagination->totalCount ?>
        </div>

    </div>
