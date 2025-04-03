<?php

namespace app\modules\admin\services;

use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ServiceService
{
    /**
     * Получение списка сервисов с количеством заказов для каждого
     *
     * @param ActiveQuery $query Запрос для подсчета заказов
     * @return array Массив с информацией о сервисах и количестве заказов
     */
    public function getServicesWithOrderCounts(ActiveQuery $query): array
    {
        $serviceCounts = $query
            ->select(['service_id', 'COUNT(*) AS count'])
            ->groupBy('service_id')
            ->asArray()
            ->all();

        $services = $this->getAllServices();

        $serviceCounts = ArrayHelper::map($serviceCounts, 'service_id', 'count');

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

        return $servicesCount;
    }

    /**
     * Получение списка [id, name] всех сервисов
     *
     * @param bool $indexById
     * @return array
     */
    public function getAllServices(bool $indexById = true): array
    {
        $services = (new Query())
            ->select(['id', 'name'])
            ->from('services')
            ->all();

        return $indexById ? ArrayHelper::map($services, 'id', 'name') : $services;
    }
}