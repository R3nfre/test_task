<?php

namespace app\modules\admin\widgets\orders;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

class DropdownFilterWidget extends Widget
{
    /** @var array Параметры запроса */
    public $params = [];

    /** @var string Заголовок выпадающего фильтра */
    public $title;

    /** @var array Массив элементов фильтра */
    public $items = [];

    /** @var string|null Текущее выбранное значение */
    public $currentValue = null;

    /** @var string Имя GET-параметра */
    public $paramName = 'service';

    /** @var boolean Показывать ли счетчики */
    public $showCounts = true;

    /** @var int|null Общее количество */
    public $totalCount = null;

    /** @var string Путь к view */
    public $viewPath = '@app/modules/admin/views/orders/dropdown_filter';

    /** @var string Базовый маршрут для ссылок */
    public $baseRoute = '/admin/orders';

    public function run()
    {
        return $this->render($this->viewPath, [
            'title' => Html::encode($this->title),
            'allLinkHtml' => $this->renderAllLink(),
            'itemsHtml' => $this->renderItems()
        ]);
    }

    /**
     * Формирует HTML для пункта "Все"
     *
     * @return string
     */
    protected function renderAllLink(): string
    {
        unset($this->params[$this->paramName]);

        $allActiveClass = $this->currentValue === null ? 'active' : '';
        $allUrl = Url::to(array_merge([$this->baseRoute], $this->params));

        $allText = Yii::t('order', 'all');

        if ($this->showCounts && $this->totalCount !== null) {
            $allText .= ' ' . Html::tag('span', "({$this->totalCount})");
        }

        return Html::tag(
            'li',
            Html::a($allText, $allUrl),
            ['class' => $allActiveClass]
        );
    }

    /**
     * Формирует HTML для всех пунктов фильтра
     *
     * @return string
     */
    protected function renderItems(): string
    {
        $itemsHtml = '';

        foreach ($this->items as $itemId => $itemData) {
            $itemsHtml .= $this->renderItem($itemId, $itemData);
        }

        return $itemsHtml;
    }

    /**
     * Формирует HTML для одного пункта фильтра
     *
     * @param int|string $itemId
     * @param array|string $itemData
     * @return string
     */
    protected function renderItem(int|string $itemId, array|string $itemData): string
    {
        $this->params[$this->paramName] = $itemId;

        $isActive = ($this->currentValue !== null && $this->currentValue == $itemId);
        $classes = $isActive ? ['active'] : [];

        $count = is_array($itemData) && isset($itemData['count']) ? $itemData['count'] : null;
        $isDisabled = $this->showCounts && $count === 0;

        if ($isDisabled) {
            $classes[] = 'disabled';
        }

        $url = $isDisabled
            ? 'javascript:void(0);'
            : Url::to(array_merge([$this->baseRoute], $this->params));

        $itemName = is_array($itemData) ? $itemData['name'] : $itemData;
        $linkContent = $this->formatItemContent($itemName, $count);

        $link = Html::a($linkContent, $url);

        return Html::tag('li', $link, ['class' => implode(' ', $classes)]);
    }

    /**
     * Форматирует содержимое пункта фильтра для добавления каунтера, нужен
     *
     * @param string $itemName
     * @param int|null $count
     * @return string
     */
    protected function formatItemContent(string $itemName, int $count = null): string
    {
        $countHtml = '';

        if ($this->showCounts && $count !== null) {
            $countHtml = Html::tag('span', $count, ['class' => 'label-id']) . ' ';
        }

        return $countHtml . Html::encode($itemName);
    }
}