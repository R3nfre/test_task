<?php

namespace app\modules\admin\controllers;

use app\modules\admin\models\search\OrdersSearch;
use app\modules\admin\services\OrdersExportService;
use app\modules\admin\services\ServiceService;
use Yii;
use yii\base\ExitException;
use yii\web\Controller;

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
        $searchModel = new OrdersSearch();
        $searchModel->load(Yii::$app->request->get());
        $dataProvider = $searchModel->search();

        $servicesCount = $this->serviceService->getServicesWithOrderCounts(
            $searchModel->getFilteredQuery()
        );

        return $this->render('index', [
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
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search();

        $this->ordersExport->exportToCsv($dataProvider->query);
    }
}