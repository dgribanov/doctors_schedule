<?php

/* 
 * DB table 'doctors'.
 */

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Doctors extends ActiveRecord
{
    const STATUS_ACTIVE = true;
    const STATUS_INACTIVE = false;

    public static function tableName()
    {
        return 'doctors';
    }

    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'firstname'      => 'Имя',
            'surname'        => 'Фамилия',
            'patronymic'     => 'Отчество',
            'specialization' => 'Специализация',
            'status'         => 'Статус',
            'name'           => 'ФИО'
        ];
    }

    public function getName ()
    {
        return $this->surname . ' ' . $this->patronymic . ' ' . $this->firstname;
    }
}