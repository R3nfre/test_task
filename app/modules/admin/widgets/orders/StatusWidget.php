<?php

namespace app\modules\admin\widgets\orders;

use app\modules\admin\models\Orders;
use app\modules\admin\models\search\OrdersSearch;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class StatusWidget extends Widget
{
    public OrdersSearch $searchModel;

    public array $params = [];

    public function run()
    {
        $currentStatus = $this->searchModel->status;
        $this->params = $this->searchModel->getFilteredAttributes();

        unset($this->params['service_id']);
        unset($this->params['mode']);

        $items = [];

        // Вкладка "Все"
        $allActiveClass = $currentStatus === null ? 'active' : '';
        $allUrl = Url::to(array_merge(['/admin/orders'], $this->params));
        $items[] = Html::tag('li', Html::a(Yii::t('order', 'model.order.status.all'), $allUrl), ['class' => $allActiveClass]);

        // Вкладки для каждого статуса
        foreach (Orders::getStatusNameList() as $orderStatusId => $orderStatusName) {
            $isActive = ($currentStatus !== null && $currentStatus == $orderStatusId);
            $activeClass = $isActive ? 'active' : '';

            $currentUrl = Url::to(array_merge([
                '/admin/orders/' . Orders::getStatusUrlKey($orderStatusId)
            ], $this->params));
            $items[] = Html::tag('li', Html::a($orderStatusName, $currentUrl), ['class' => $activeClass]);
        }

        return implode("\n", $items);
    }
}