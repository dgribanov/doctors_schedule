<?php

/* 
 * DB table 'patients'.
 */

namespace app\models;

use yii\db\ActiveRecord;

class Patients extends ActiveRecord
{
    public static function tableName()
    {
        return 'patients';
    }

    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'firstname'      => 'Имя',
            'surname'        => 'Фамилия',
            'patronymic'     => 'Отчество',
        ];
    }

    public function rules()
    {
        return [
            [ ['firstname', 'surname', 'patronymic'], 'required' ]
        ];
    }
}