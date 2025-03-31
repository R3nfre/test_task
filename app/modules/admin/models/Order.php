<?php

namespace app\modules\admin\models;

use Yii;
use yii\db\ActiveRecord;

class Order extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELED = 3;
    const STATUS_FAIL = 4;

    public static function tableName()
    {
        return 'orders';
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_PENDING => Yii::t('order', 'pending'),
            self::STATUS_IN_PROGRESS => Yii::t('order', 'in_progress'),
            self::STATUS_COMPLETED => Yii::t('order', 'completed'),
            self::STATUS_CANCELED => Yii::t('order', 'canceled'),
            self::STATUS_FAIL => Yii::t('order', 'fail'),
        ];
    }

    public function getStatusName()
    {
        return self::getStatusList()[$this->status] ?? Yii::t('orders', 'unknown');
    }

    public function rules()
    {
        return [
            [['user_id', 'link', 'quantity', 'service_id', 'status', 'created_at', 'mode'], 'required'],
            [['user_id', 'quantity', 'service_id', 'status', 'created_at', 'mode'], 'integer'],
            [['link'], 'string', 'max' => 300],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'link' => 'Link',
            'quantity' => 'Quantity',
            'service_id' => 'Service ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'mode' => 'Mode',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getService()
    {
        return $this->hasOne(Service::class, ['id' => 'service_id']);
    }
}
