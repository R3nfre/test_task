<?php

namespace app\modules\admin\services;

use app\modules\admin\models\order\Order;
use Yii;
use yii\db\Query;

class OrdersExportService
{
    /**
     * Экспортирует заказы в CSV файл
     *
     * @param Query|null $query Запрос для выборки заказов
     * @return void
     */
    public function exportToCsv(Query|null $query): void
    {
        $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';

        try {
            $this->prepareForCsvOutput();
            $this->sendCsvHeaders($filename);
            $this->writeCsvContent($this->optimizeOrderQuery($query));
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
        return $query->select([
            'orders.id',
            'users.first_name',
            'users.last_name',
            'orders.link',
            'orders.quantity',
            'services.name as service_name',
            'orders.status',
            'orders.mode',
            'orders.created_at'
        ])
            ->leftJoin('users', 'orders.user_id = users.id')
            ->leftJoin('services', 'orders.service_id = services.id');
    }

    /**
     * Записывает содержимое CSV
     *
     * @param Query $query
     */
    private function writeCsvContent(Query $query): void
    {
        ob_start();

        $headers = ['ID', 'User Name', 'Link', 'Quantity', 'Service Name', 'Status', 'Mode', 'Created At'];
        $csvLine = '"' . implode('","', $headers) . '"' . "\n";
        echo $csvLine;

//        ob_flush();
        flush();

        $formatter = Yii::$app->formatter;

        foreach ($query->batch() as $orders) {
            $batchOutput = '';

            foreach ($orders as $order) {
                $row = [
                    $order['id'],
                    ($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? ''),
                    $order['link'] ?? '',
                    $order['quantity'] ?? '',
                    $order['service_name'] ?? '',
                    Order::getStatusList()[$order['status']],
                    Order::getModeList()[$order['mode']],
                    $formatter->asDatetime($order['created_at'], 'php:Y-m-d H:i:s')
                ];

                // Экранируем и форматируем строку CSV
                $escapedRow = array_map([$this, 'escapeCsvField'], $row);
                $batchOutput .= '"' . implode('","', $escapedRow) . '"' . "\n";
            }

            echo $batchOutput;

//            ob_flush();
            flush();

            unset($orders, $batchOutput);
            gc_collect_cycles();
        }

//        ob_end_flush();
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