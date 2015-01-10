<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Это тестовый сайт, реализующий формуляр записи на приём к врачу.</p>
    <p>Создан на базе Yii 2 Framework с использованием jQuery, MySQL и AJAX.</p>
    <p>
        Адрес проекта на GitHub: <a href="https://github.com/dgribanov/doctors_schedule">https://github.com/dgribanov/doctors_schedule</a>
    </p>

</div>
