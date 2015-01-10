<?php

/* 
 * DB table 'doctors_specialization'.
 */

namespace app\models;

use yii\db\ActiveRecord;

class DoctorSpecialization extends ActiveRecord
{
    public static function tableName()
    {
        return 'doctors_specialization';
    }

    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'specialization' => 'Специализация',
        ];
    }
}