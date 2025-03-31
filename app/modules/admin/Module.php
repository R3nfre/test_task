<?php

namespace app\modules\admin;

use Yii;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();
        $this->layoutPath = '@app/modules/admin/views/layouts';
        $this->layout = 'main';


        Yii::$app->i18n->translations['order'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/admin/messages',
        ];

        Yii::$app->language = 'ru';
    }
}