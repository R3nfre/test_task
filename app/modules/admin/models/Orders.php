<?php

namespace app\modules\admin\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class Orders extends ActiveRecord
{
    const STATUS_PENDING = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_CANCELED = 3;
    const STATUS_FAIL = 4;

    const MODE_MANUAL = 0;
    const MODE_AUTO = 1;

    public static function tableName()
    {
        return 'orders';
    }

    public static function getStatusNameList(): array
    {
        return [
            self::STATUS_PENDING => Yii::t('order', 'model.order.status.pending'),
            self::STATUS_IN_PROGRESS => Yii::t('order', 'model.order.status.in_progress'),
            self::STATUS_COMPLETED => Yii::t('order', 'model.order.status.completed'),
            self::STATUS_CANCELED => Yii::t('order', 'model.order.status.canceled'),
            self::STATUS_FAIL => Yii::t('order', 'model.order.status.fail'),
        ];
    }

    public static function getStatusUrlKeys(): array
    {
        return [
            self::STATUS_PENDING => 'pending',
            self::STATUS_IN_PROGRESS => 'in_progress',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_CANCELED => 'canceled',
            self::STATUS_FAIL => 'fail',
        ];
    }

    public static function getStatusUrlKey(int $status): ?string
    {
        return self::getStatusUrlKeys()[$status] ?? null;
    }

    public static function getStatusByUrlKey(string $urlKey): ?int
    {
        $reverseMap = array_flip(self::getStatusUrlKeys());
        return $reverseMap[$urlKey] ?? null;
    }
    public function getStatusName()
    {
        return self::getStatusNameList()[$this->status] ?? Yii::t('orders','model.order.status.unknown');
    }

    public static function getModeNameList(): array
    {
        return [
            self::MODE_MANUAL => Yii::t('order', 'model.order.mode.manual'),
            self::MODE_AUTO => Yii::t('order', 'model.order.mode.auto'),
        ];
    }

    public function getModeName()
    {
        return self::getModeNameList()[$this->mode] ?? Yii::t('order','model.order.status.unknown');
    }

    public function rules()
    {
        return [
            [['user_id', 'link', 'quantity', 'service_id', 'status', 'created_at', 'mode'], 'required'],
            [['user_id', 'quantity', 'service_id', 'status', 'created_at', 'mode'], 'integer'],
            [['link'], 'string', 'max' => 300],
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getService(): ActiveQuery
    {
        return $this->hasOne(Service::class, ['id' => 'service_id']);
    }
}
