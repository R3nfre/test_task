<?php

namespace app\modules\admin\widgets\orders;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class SearchWidget extends Widget
{
    /**
     * @var array Параметры запроса
     */
    public $params = [];
    /**
     * @var string URL для формы поиска
     */
    public $actionUrl = '/admin/orders';

    /**
     * @var string Путь к представлению
     */
    public $viewPath = '@app/modules/admin/views/orders/search';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $searchValue = $this->params['search'] ?? '';
        $currentTypeValue = $this->params['search_type'] ?? 'id';
        $currentStatus = $this->params['status'] ?? null;

        $searchTypes = [
            'id' => Yii::t('order', 'id_order_name'),
            'link' => Yii::t('order', 'link_name'),
            'name' => Yii::t('order', 'username_name')
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
            'actionUrl' => Html::encode($this->actionUrl),
            'searchValue' => Html::encode($searchValue),
            'optionsHtml' => $optionsHtml,
            'statusInputHtml' => $statusInputHtml,
            'searchPlaceholder' => Yii::t('order', 'search_orders_name')
        ]);
    }
}