<?php

namespace app\modules\admin\controllers;

use app\modules\admin\models\order\Order;
use app\modules\admin\models\order\OrderSearch;
use app\modules\admin\services\OrdersExportService;
use app\modules\admin\services\ServiceService;
use app\modules\admin\services\OrderService;
use SebastianBergmann\Type\VoidType;
use Yii;
use yii\base\ExitException;
use yii\web\Controller;
use yii\web\Response;

class OrdersController extends Controller
{
    public function __construct(
        mixed $id,
        mixed $module,
        private ServiceService $serviceService,
        private OrdersExportService $ordersExport,
        array $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $servicesCount = $this->serviceService->getServicesWithOrderCounts(
            $searchModel->getFilteredQuery(Yii::$app->request->queryParams)
        );

        return $this->render('index', [
            'params' => Yii::$app->request->getQueryParams(),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'servicesCount' => $servicesCount,
            'totalCountWithoutSectionFilter' => $searchModel->getTotalCountWithoutServiceFilter(),
        ]);
    }

    /**
     * Экспортирует отфильтрованный список заказов в CSV-файл
     *
     * @throws ExitException
     */
    public function actionDownloadCsv()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->ordersExport->exportToCsv($dataProvider->query);
    }
}