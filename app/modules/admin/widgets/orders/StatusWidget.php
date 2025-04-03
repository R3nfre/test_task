<?php

namespace app\modules\admin\widgets\orders;

use app\modules\admin\models\order\Order;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class StatusWidget extends Widget
{
    /**
     * @var array Параметры запроса
     */
    public array $params = [];
    public function run()
    {
        // Получаем параметры запроса
        $currentStatus = $this->params['status'] ?? null;

        unset($this->params['service_id']);
        unset($this->params['mode']);
        unset($this->params['status']);

        $items = [];

        // Вкладка "Все"
        $allActiveClass = $currentStatus === null ? 'active' : '';
        $allUrl = Url::to(array_merge(['/admin/orders'], $this->params));
        $items[] = Html::tag('li', Html::a(Yii::t('order', 'all'), $allUrl), ['class' => $allActiveClass]);

        // Вкладки для каждого статуса
        foreach (Order::getStatusList() as $orderStatusId => $orderStatusName) {
            $isActive = ($currentStatus !== null && $currentStatus == $orderStatusId);
            $activeClass = $isActive ? 'active' : '';

            $statusParams = $this->params;
            $statusParams['status'] = $orderStatusId;

            $currentUrl = Url::to(array_merge(['/admin/orders'], $statusParams));
            $items[] = Html::tag('li', Html::a($orderStatusName, $currentUrl), ['class' => $activeClass]);
        }

        return implode("\n", $items);
    }
}