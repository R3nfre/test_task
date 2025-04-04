<?php

namespace app\modules\admin\models\query;

use app\modules\admin\models\Orders;

/**
 * This is the ActiveQuery class for [[\app\modules\admin\models\Orders]].
 *
 * @see \app\modules\admin\models\Orders
 */
class OrdersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Orders[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return array|\yii\db\ActiveRecord|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
