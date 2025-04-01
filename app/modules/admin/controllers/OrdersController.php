<?php

namespace app\modules\admin\controllers;

use app\modules\admin\models\Order;
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
        $mode = Yii::$app->request->get('mode');
        $service_id = Yii::$app->request->get('service_id');
        $search = Yii::$app->request->get('search');
        $searchType = Yii::$app->request->get('search-type');

        $query = Order::find();

        if ($status !== null) {
            $query->andWhere(['status' => $status]);
        }

        if ($mode !== null) {
            $query->andWhere(['mode' => $mode]);
        }

        if (!empty($search)) {
            if ($searchType === 'name' || $searchType === null) {
                $query->joinWith(['user']);
            }

            switch ($searchType) {
                case 'id':
                    $query->andWhere(['like', 'orders.id',  $search]);
                    break;

                case 'name':
                    $query->andWhere([
                        'or',
                        ['like', 'users.first_name', $search],
                        ['like', 'users.last_name', $search],
                    ]);
                    break;

                case 'link':
                    $query->andWhere(['like', 'orders.link', $search]);
                    break;
            }
        }

        $totalCountWithoutSectionFilter = $query->count();

        $servicesQuery = clone $query;

        if ($service_id !== null) {
            $query->andWhere(['service_id' => $service_id]);
        }

        if ($service_id !== null) {
            $query->andWhere(['service_id' => $service_id]);
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

        $serviceCounts = $servicesQuery
            ->select(['service_id', 'COUNT(*) AS count'])
            ->groupBy('service_id')
            ->asArray()
            ->all();

        $services = (new Query())
            ->select(['id', 'name'])
            ->from('services')
            ->all();

        $serviceCounts = ArrayHelper::map($serviceCounts, 'service_id', 'count');
        $services = ArrayHelper::map($services, 'id', 'name');

        $servicesCount = [];

        foreach ($services as $serviceId => $serviceName) {
            $servicesCount[$serviceId] = [
                'service_name' => $serviceName,
                'service_count' => $serviceCounts[$serviceId] ?? 0,
            ];
        }

        uasort($servicesCount, function($a, $b) {
            return $b['service_count'] - $a['service_count'];
        });

        return $this->render('index', [
            'pagination' => $pagination,
            'orders' => $orders,
            'servicesCount' => $servicesCount,
            'totalCountWithoutSectionFilter' => $totalCountWithoutSectionFilter,
        ]);
    }
}