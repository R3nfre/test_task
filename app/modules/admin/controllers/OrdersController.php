<?php

namespace app\modules\admin\controllers;

use app\modules\admin\models\Order;
use app\modules\admin\models\Service;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class OrdersController extends Controller
{
    public function behaviors()
    {
        return [];
    }

    public function actionIndex()
    {
        $pageSize = 100;

        $status = Yii::$app->request->get('status');
        $service_id = Yii::$app->request->get('service_id');

        $query = Order::find();

        $serviceCountsQuery = (new Query())
            ->select(['service_id', 'COUNT(*) AS count'])
            ->from('orders')
            ->groupBy('service_id');

        if ($status !== null) {
            $query->andWhere(['status' => $status]);
            $serviceCountsQuery->andWhere(['status' => $status]);
        }

        if ($service_id !== null) {
            $query->andWhere(['service_id' => $service_id]);
            $serviceCountsQuery->andWhere(['service_id' => $service_id]);
        }
        $totalCount = $query->count();

        $pagination = new Pagination([
            'totalCount' => $totalCount,
            'pageSize' => $pageSize,
        ]);


        $orders = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $serviceCounts = $serviceCountsQuery->all();

        $services = (new Query())
            ->select(['id', 'name'])
            ->from('services')
            ->all();

        $serviceCounts = ArrayHelper::map($serviceCounts, 'service_id', 'count');
        $services = ArrayHelper::map($services, 'id', 'name');

        $servicesCount = [];

        foreach ($services as $serviceId => $serviceName) {
            $servicesCount[$serviceName] = $serviceCounts[$serviceId] ?? 0;
        }

        arsort($servicesCount);


        return $this->render('index', [
            'pagination' => $pagination,
            'orders' => $orders,
            'servicesCount' => $servicesCount,
        ]);
    }
}