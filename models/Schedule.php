<?php

/* 
 * DB table 'schedule'.
 */

namespace app\models;

use yii\db\ActiveRecord;

class Schedule extends ActiveRecord
{
    public $RESERVED;

    public static function tableName()
    {
        return 'schedule';
    }

    public function attributeLabels()
    {
        return [
            'doctor_id'      => 'Врач',
            'patient_id'     => 'Пациент',
            'date'           => 'Дата приёма',
            'time_id'        => 'Время приёма'
        ];
    }
}