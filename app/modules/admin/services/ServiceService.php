<?php

namespace app\modules\admin\services;

use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ServiceService
{
    /**
     * @param ActiveQuery $query
     * @return array
     */
    public function getServicesWithOrderCounts(ActiveQuery $query): array
    {
        $query = clone $query;

        $query->orderBy([]);

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