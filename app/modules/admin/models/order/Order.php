<?php

namespace app\modules\admin\models\order;

use app\modules\admin\models\service\Service;
use app\modules\admin\models\user\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class Order extends ActiveRecord
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

    public static function getStatusList(): array
    {
        return [
            self::STATUS_PENDING => Yii::t('order', 'status_pending'),
            self::STATUS_IN_PROGRESS => Yii::t('order', 'status_in_progress'),
            self::STATUS_COMPLETED => Yii::t('order', 'status_completed'),
            self::STATUS_CANCELED => Yii::t('order', 'status_canceled'),
            self::STATUS_FAIL => Yii::t('order', 'status_fail'),
        ];
    }

    public function getStatusName()
    {
        return self::getStatusList()[$this->status] ?? Yii::t('orders', 'unknown');
    }

    public static function getModeList(): array
    {
        return [
            self::MODE_MANUAL => Yii::t('order', 'mode_manual'),
            self::MODE_AUTO => Yii::t('order', 'mode_auto'),
        ];
    }

    public function getModeName()
    {
        return self::getModeList()[$this->mode] ?? Yii::t('order', 'unknown');
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
