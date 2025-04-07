<?php

namespace app\modules\admin\services;

use app\modules\admin\models\Orders;
use app\modules\admin\models\search\OrdersSearch;
use Yii;
use yii\db\Query;

class OrdersExportService
{
    public OrdersSearch $searchModel;

    /**
     *
     * @param OrdersSearch $searchModel
     * @return void
     */
    public function exportToCsv(OrdersSearch $searchModel): void
    {
        $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';

        try {
            $this->searchModel = $searchModel;

            $this->searchModel->load(Yii::$app->request->get());

            $this->prepareForCsvOutput();
            $this->sendCsvHeaders($filename);
            $this->writeCsvContent($this->optimizeOrderQuery($this->searchModel->getFilteredQuery()));
        } catch (\Exception $e) {
            Yii::error('CSV export error: ' . $e->getMessage(), __METHOD__);
        }
    }

    /**
     * Подготавливает окружение для вывода CSV
     */
    private function prepareForCsvOutput(): void
    {
        // Отключаем логирование
        if (Yii::$app->has('log')) {
            foreach (Yii::$app->log->targets as $target) {
                $target->enabled = false;
            }
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    /**
     * Отправляет HTTP заголовки для скачивания CSV
     *
     * @param string $filename
     */
    private function sendCsvHeaders(string $filename): void
    {
        header('Content-Encoding: none');
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    /**
     * Оптимизирует запрос для получения данных заказов
     *
     * @param Query $query
     * @return Query
     */
    private function optimizeOrderQuery(Query $query): Query
    {
        $query->select([
            'orders.id',
            'orders.user_id',
            'orders.service_id',
            'users.first_name',
            'users.last_name',
            'orders.link',
            'orders.quantity',
            'services.name as service_name',
            'orders.status',
            'orders.mode',
            'orders.created_at'
        ])->leftJoin('services', 'orders.service_id = services.id');


        if($this->searchModel->search_type !== 'name') {
            $query->leftJoin('users', 'orders.user_id = users.id');
        }

        return $query;
    }

    /**
     * @param Query $query
     */
    private function writeCsvContent(Query $query): void
    {
        ob_start();

        $headers = ['ID', 'User Name', 'Link', 'Quantity', 'Service Name', 'Status', 'Mode', 'Created At'];
        $csvLine = '"' . implode('","', $headers) . '"' . "\n";
        echo $csvLine;

        flush();

        $formatter = Yii::$app->formatter;

        $limit = 100;
        $offset = 0;

        while ($orders = $query->limit($limit)->offset($offset)->all()) {
            foreach ($orders as $order) {
                $row = [
                    $order->id,
                    ($order->user->first_name ?? '') . ' ' . ($order->user->last_name ?? ''),
                    $order->link ?? '',
                    $order->quantity ?? '',
                    $order->service->name ?? '',
                    Orders::getStatusNameList()[$order->status],
                    Orders::getModeNameList()[$order->mode],
                    $formatter->asDatetime($order->created_at, 'php:Y-m-d H:i:s')
                ];

                $escapedRow = array_map([$this, 'escapeCsvField'], $row);
                echo '"' . implode('","', $escapedRow) . '"' . "\n";
            }

            flush();

            $offset += $limit;

            unset($orders);
            gc_collect_cycles();
        }
    }

    /**
     * Экранирует поле для CSV
     *
     * @param mixed $field
     * @return string
     */
    private function escapeCsvField(mixed $field): string
    {
        if (is_null($field)) return '';
        // Экранируем двойные кавычки двойными кавычками
        return str_replace('"', '""', $field);
    }
}