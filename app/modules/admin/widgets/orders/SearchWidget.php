<?php

namespace app\modules\admin\widgets\orders;

use app\modules\admin\models\Orders;
use app\modules\admin\models\search\OrdersSearch;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class SearchWidget extends Widget
{
    /**
     * @var OrdersSearch Модель поиска
     */
    public OrdersSearch $searchModel;
    /**
     * @var string URL для формы поиска
     */
    public string $baseRoute = '/admin/orders';

    /**
     * @var string Путь к представлению
     */
    public $viewPath = '@app/modules/admin/views/orders/search';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $searchValue = $this->searchModel->search ?? '';
        $currentTypeValue = $this->searchModel->search_type ?? 'id';
        $currentStatus = $this->searchModel->status;
        $this->baseRoute .= $this->searchModel->status ? '/' . Orders::getStatusUrlKey($this->searchModel->status) : '';

        $searchTypes = [
            'id' => Yii::t('order', 'table.search.dropdown.order_id.name'),
            'link' => Yii::t('order', 'table.header.link.name'),
            'name' => Yii::t('order', 'table.search.dropdown.user.name')
        ];

        $optionsHtml = '';
        foreach ($searchTypes as $typeValue => $typeName) {
            $optionsHtml .= Html::tag('option', Html::encode($typeName), [
                'value' => $typeValue,
                'selected' => $typeValue === $currentTypeValue
            ]);
        }

        $statusInputHtml = '';
        if ($currentStatus) {
            $statusInputHtml = Html::hiddenInput('status', $currentStatus);
        }

        return $this->render($this->viewPath, [
            'actionUrl' => Html::encode($this->baseRoute),
            'searchValue' => Html::encode($searchValue),
            'optionsHtml' => $optionsHtml,
            'statusInputHtml' => $statusInputHtml,
            'searchPlaceholder' => Yii::t('order', 'table.search.placeholder.name')
        ]);
    }
}