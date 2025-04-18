<?php

namespace app\modules\admin\models\search;

use app\modules\admin\models\Orders;
use app\modules\admin\models\Service;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class OrdersSearch extends Model
{
    /**
     * Размер страницы для пагинации
     */
    const PAGE_SIZE = 100;

    public $mode;
    public $service_id;
    public $search;
    public $search_type;
    public $status;
    private $queryWithoutServiceFilter;

    /**
     * @var ActiveQuery Базовый запрос
     */
    private $query;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mode', 'service_id', 'status'], 'integer'],
            [['search', 'search_type'], 'string'],

            ['search', 'validateSearchPair'],
            ['search_type', 'validateSearchPair'],

            ['mode', 'in', 'range' => [Orders::MODE_MANUAL, Orders::MODE_AUTO]],

            ['status', 'validateStatus'],

            ['service_id', 'validateServiceId'],
        ];
    }

    /**
     * @param $data
     * @param string|null $formName
     * @return bool
     */
    public function load($data, $formName = ''): bool
    {
        $allParams = $formName === '' ? $data : $data[$formName] ?? [];

        $result = parent::load($data, $formName);

        if ($result) {
            if (isset($allParams['status'])) {
                $this->status = Orders::getStatusByUrlKey($allParams['status']);
            }
            $this->validateAttributes();
        }

        return $result;
    }

    protected function validateAttributes(): void
    {
        if (!$this->validateSearchPair()) {
            $this->search = null;
            $this->search_type = null;
        }

        if (!$this->validateMode()) {
            $this->mode = null;
        }

        if (!$this->validateStatus()) {
            $this->status = '';
        }

        if (!$this->validateServiceId()) {
            $this->service_id = null;
        }
    }

    protected function validateSearchPair(): bool
    {
        if ((empty($this->search) && !empty($this->search_type)) ||
            (!empty($this->search) && empty($this->search_type))) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function validateMode()
    {
        if ($this->mode === null) {
            return true;
        }

        return in_array($this->mode, [Orders::MODE_MANUAL, Orders::MODE_AUTO]);
    }

    /**
     * @return bool
     */
    protected function validateStatus(): bool
    {
        if ($this->status === null) {
            return true;
        }

        $statusKeys = array_keys(Orders::getStatusUrlKeys());
        return in_array($this->status, $statusKeys);
    }

    /**
     * @return bool
     */
    protected function validateServiceId(): bool
    {
        if ($this->service_id === null) {
            return true;
        }

        return Service::find()->where(['id' => $this->service_id])->exists();
    }

    /**
     * @return array
     */
    public function getFilteredAttributes(): array
    {
        $result = [];

        if ($this->mode !== null) {
            $result['mode'] = $this->mode;
        }

        if ($this->service_id !== null) {
            $result['service_id'] = $this->service_id;
        }

        if ($this->search !== null && $this->search_type !== null) {
            $result['search'] = $this->search;
            $result['search_type'] = $this->search_type;
        }

        return $result;
    }

    /**
     * @return ActiveDataProvider
     */
    public function search(): ActiveDataProvider
    {
        $this->buildQuery();

        $this->queryWithoutServiceFilter = clone $this->query;

        $this->applyServiceFilter();

        return new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => self::PAGE_SIZE,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);
    }

    /**
     * Построение базового запроса с применением фильтров
     *
     * @return ActiveQuery
     */
    public function buildQuery(): ActiveQuery
    {
        $this->query = Orders::find();

        $this->applyStatusFilter();
        $this->applyModeFilter();
        $this->applySearchFilter();

        return $this->query;
    }

    protected function applyStatusFilter(): void
    {
        if ($this->status !== null) {
            $this->query->andWhere(['status' => $this->status]);
        }
    }

    protected function applyModeFilter(): void
    {
        if ($this->mode !== null) {
            $this->query->andWhere(['mode' => $this->mode]);
        }
    }

    protected function applyServiceFilter(): void
    {
        if ($this->service_id !== null) {
            $this->query->andWhere(['service_id' => $this->service_id]);
        }
    }

    protected function applySearchFilter(): void
    {
        if (empty($this->search) || empty($this->search_type)) {
            return;
        }

        if ($this->search_type === 'name') {
            $this->query->joinWith(['user']);
        }

        switch ($this->search_type) {
            case 'id':
                $this->applyIdSearchFilter();
                break;

            case 'name':
                $this->applyNameSearchFilter();
                break;

            case 'link':
                $this->applyLinkSearchFilter();
                break;
        }
    }

    /**
     * Применяет фильтр поиска по ID
     */
    protected function applyIdSearchFilter(): void
    {
        $this->query->andWhere(['like', 'orders.id', $this->search]);
    }

    /**
     * Применяет фильтр поиска по ссылке
     */
    protected function applyLinkSearchFilter(): void
    {
        $this->query->andWhere(['like', 'orders.link', $this->search]);
    }

    /**
     * Применяет фильтр поиска по имени фамилии пользователя
     *
     * @return void
     */
    protected function applyNameSearchFilter(): void
    {
        $search = trim($this->search);
        $parts = preg_split('/\s+/', $search);

        if (count($parts) > 1) {
            $firstName = array_shift($parts);
            $lastName = implode(' ', $parts);

            $this->query->andWhere([
                'or',
                ['and',
                    ['like', 'users.first_name', $firstName . '%', false],
                    ['like', 'users.last_name', $lastName . '%', false]
                ],
                ['and',
                    ['like', 'users.first_name', $lastName . '%', false],
                    ['like', 'users.last_name', $firstName . '%', false]
                ],
                ['like', 'CONCAT(users.first_name, " ", users.last_name)', $search],
                ['like', 'CONCAT(users.last_name, " ", users.first_name)', $search]
            ]);
        } else {
            $this->query->andWhere([
                'or',
                ['like', 'users.first_name', $search . '%', false],
                ['like', 'users.last_name', $search . '%', false]
            ]);
        }
    }

    /**
     * @return ActiveQuery
     */
    public function getFilteredQuery(): ActiveQuery
    {
        if ($this->query === null) {
            $this->buildQuery();
        }

        return $this->query;
    }

    /**
     * @return ActiveQuery
     */
    public function getFilteredQueryWithoutServiceFilter(): ActiveQuery
    {
        return $this->queryWithoutServiceFilter;
    }

    /**
     * Получение общего количества заказов без фильтра по сервису
     *
     * @return int
     */
    public function getTotalCountWithoutServiceFilter(): int
    {
        return $this->queryWithoutServiceFilter ? $this->queryWithoutServiceFilter->count() : 0;
    }
}