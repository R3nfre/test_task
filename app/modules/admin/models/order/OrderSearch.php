<?php

namespace app\modules\admin\models\order;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * OrderSearch - модель поиска для заказов
 */
class OrderSearch extends Model
{
    /**
     * Размер страницы для пагинации
     */
    const PAGE_SIZE = 100;

    /**
     * @var string Статус заказа
     */
    public $status;

    /**
     * @var string Режим заказа
     */
    public $mode;

    /**
     * @var int ID сервиса
     */
    public $service_id;

    /**
     * @var string Поисковый запрос
     */
    public $search;

    /**
     * @var string Тип поиска (id, name, link)
     */
    public $search_type;

    /**
     * @var ActiveQuery Запрос без фильтра по сервису
     */
    private $queryWithoutServiceFilter;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'mode', 'search', 'search_type'], 'string'],
            [['service_id'], 'integer'],
        ];
    }

    /**
     * Поиск заказов с примененными фильтрами
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $this->load($params, '');

        $query = $this->getFilteredQuery($params);

        $this->queryWithoutServiceFilter = clone $query;

        if ($this->service_id !== null) {
            $query->andWhere(['service_id' => $this->service_id]);
        }

        return new ActiveDataProvider([
            'query' => $query,
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
     * @param array $params
     * @return ActiveQuery
     */
    public function getFilteredQuery(array $params): ActiveQuery
    {
        if (!$this->load($params, '')) {
            $this->load($params, '');
        }

        $query = Order::find();

        if ($this->status !== null && $this->status !== '') {
            $query->andWhere(['status' => $this->status]);
        }

        if ($this->mode !== null && $this->mode !== '') {
            $query->andWhere(['mode' => $this->mode]);
        }

        if (!empty($this->search)) {
            $this->applySearchFilter($query);
        }

        return $query;
    }

    /**
     * Применение поискового фильтра к запросу
     *
     * @param ActiveQuery $query
     */
    private function applySearchFilter(ActiveQuery $query): void
    {
        if ($this->search_type === 'name') {
            $query->joinWith(['user']);
        }

        switch ($this->search_type) {
            case 'id':
                $query->andWhere(['like', 'orders.id', $this->search]);
                break;

            case 'name':
                $this->applyNameSearchFilter($query);
                break;

            case 'link':
                $query->andWhere(['like', 'orders.link', $this->search]);
                break;
        }
    }

    /**
     * Применяет фильтр поиска по имени фамилии пользователя
     *
     * @param ActiveQuery $query
     * @return void
     */
    private function applyNameSearchFilter(ActiveQuery $query): void
    {
        $search = trim($this->search);
        $parts = preg_split('/\s+/', $search);

        if (count($parts) > 1) {
            $firstName = array_shift($parts);
            $lastName = implode(' ', $parts);

            $query->andWhere([
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
            $query->andWhere([
                'or',
                ['like', 'users.first_name', $search . '%', false],
                ['like', 'users.last_name', $search . '%', false]
            ]);
        }
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