<?php
/* @var $this yii\web\View */
$this->title = 'Запись на приём к врачу';
?>

<?php
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
use demogorgorn\ajax\AjaxSubmitButton;
use yii\web\JsExpression;
?>

<div class="site-index">

    <div class="jumbotron">
        <h1>Запись на приём к врачу</h1>
    </div>

<?php
    $form = ActiveForm::begin([
        'id' => 'active-form',
        'type' => ActiveForm::TYPE_VERTICAL
    ])
?>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4 active-column">
                <h2>1. Введите свои данные:</h2>

                <?= $form->field($patient, 'surname')->textInput( 
                        [
                            'id'=>'patient-surname',
                            'class' => 'patient-info',
                            'onchange' => 'selectDocSpec()'
                        ]
                    );
                ?>
                <?= $form->field($patient, 'firstname')->textInput(
                        [
                            'id'=>'patient-firstname',
                            'class' => 'patient-info',
                            'onchange' => 'selectDocSpec()'
                        ]
                    );
                ?>
                <?= $form->field($patient, 'patronymic')->textInput( 
                        [
                            'id'=>'patient-patronymic',
                            'class' => 'patient-info',
                            'onchange' => 'selectDocSpec()'
                        ]
                    );
                ?>

            </div>
            <div class="col-lg-4 not-active-column">
                <h2>2. Выберите врача:</h2>

                <?= $form->field($doctors, 'specialization')->dropDownList(
                        ArrayHelper::map($doctorSpec, 'id', 'specialization'),
                        [
                            'id'=>'doctor-spec', 
                            'prompt' => 'Выберите специализацию врача...',
                            'title' => 'Выберите специализацию врача...',
                            'disabled' => 'disabled',
                            'onchange' => 'selectDoctor(this.value)'
                        ]
                    );
                ?>
                <?= $form->field($doctors, 'name')->dropDownList( 
                        [],
                        [
                            'id'=>'doctors',
                            'prompt' => 'Выберите врача...',
                            'title' => 'Вы не выбрали специализацию врача!',
                            'disabled' => 'disabled',
                            'onchange' => 'selectDate(this.value)'
                        ]
                    );
                ?>

            </div>
            <div class="col-lg-4 not-active-column">
                <h2>3. Выберите дату визита:</h2>

                <?php
                    echo '<label class="control-label">Дата визита</label>';
                    echo DatePicker::widget([
                            'name' => 'date',
                            'type' => DatePicker::TYPE_INPUT,
                            'value' => '',
                            'options' => [
                                'id'=>'dates',
                                'placeholder' => 'Выберите дату визита к врачу...',
                                'title' => 'Вы не выбрали врача!',
                                'disabled' => 'disabled',
                                'onchange' => 'selectTime(this.value)'
                            ],
                            'pluginOptions' => [
                                'format' => 'dd-m-yyyy',
                                'autoclose' => true,
                                'daysOfWeekDisabled' => [0,6],
                                'startDate' => date('d-m-Y', strtotime("tomorrow")),
                                'endDate' => date('d-m-Y', strtotime("+3 week"))
                            ],
                            'pluginEvents' => [
                                'show' => 'showDates'
                                
                            ]
                        ]
                    );
                ?>

            </div>
            <div class="col-lg-4 not-active-column">
                <h2>4. Выберите время:</h2>

                <?php
                    echo '<label class="control-label">Время визита</label>';
                    echo DateTimePicker::widget([
                        'name' => 'time',
                        'type' => DateTimePicker::TYPE_INPUT,
                        'value' => '',
                        'pickerButton' => false,
                        'options' => [
                            'id'=>'time',
                            'placeholder' => 'Выберите время визита к врачу...',
                            'title' => 'Вы не выбрали дату визита к врачу!',
                            'disabled' => 'disabled',
                            //'onchange' => 'addSubmit(this.value)'
                        ],
                        'pluginOptions' => [
                            'format' => 'hh:00',
                            'autoclose' => true,
                            'startView' => 1,
                            'minView' => 1,
                            'maxView' => 1
                        ],
                        'pluginEvents' => [
                            'show' => 'showTime',
                            'changeDate' => 'addSubmit'
                        ]
                    ]
                    );
                ?>

            </div>
        </div>

        <div class="submitButton">

        <?php AjaxSubmitButton::begin([
            'label' => 'Записаться на приём',
            'ajaxOptions' => [
                'type'=>'POST',
                'url'=> Url::to('site/save'),
                'success' => new JsExpression('function(data){
                }'),
            ],
            'options' => ['type' => 'submit', 'id' => 'submitButton', 'disabled' => 'disabled', 'title' => 'Вы не заполнили всю форму!'],
        ]
        );
        AjaxSubmitButton::end();
        ?>

        </div>

    </div>

<?php
    ActiveForm::end();
?>

<?php
    $this->registerJs('
    function selectDocSpec() {
        var values = $(".patient-info").map(function(){return this.value;});
        if($.inArray("", values) < 0) {
            $("#doctor-spec").each( function (){ this.disabled = false; } );
        } else {
            $("#doctor-spec").each( function (){ this.disabled = "disabled"; } );
        }
    }

    function selectDoctor(id) {
        if(id.length > 0){
            $.get("'. Url::to('site/doctors') . '", {id: id}, function (data){
                if(data.length > 0){
                    sessionStorage.removeItem("dates");
                    var doctors = $.parseJSON(data);
                    $("#doctors").each( function (){ if(this.disabled != null) this.disabled = false; } );
                    $("#doctors > option:not(:first-child)").remove();
                    $("#doctors").attr("title", "Выберите врача...");
                    for(var i = 0; i < doctors.length; i++){
                        $("#doctors").append(
                            $("<option />", {value: doctors[i].id}).text(doctors[i].surname + " " + doctors[i].firstname + " " + doctors[i].patronymic)
                        );
                    }
                }
            }
            );
        } else {
            sessionStorage.removeItem("dates");
            $("#doctors > option:not(:first-child)").remove();
            $("#doctors").attr("title", "Вы не выбрали специализацию врача!");
            $("#doctors").each( function (){ this.disabled = true; } );
        }
    }

    function selectDate(id) {
        if(id.length > 0){
            $.get("'. Url::to('site/dates') . '", {id: id}, function (data){
                if(data.length > 0){
                    sessionStorage.setItem("dates", data);
                    $("#dates").each( function (){ if(this.disabled != null) this.disabled = false; } );
                    $("#dates").attr("title", "Выберите дату визита к врачу...");
                }
            }
            );
        } else {
            sessionStorage.removeItem("dates");
            $("#dates").attr("title", "Вы не выбрали врача!");
            $("#dates").each( function (){ this.disabled = true; } );
        }
    }

    function showDates(event) {
        $("div.datepicker-days td.reserved").removeClass("disabled reserved");
        var dates = sessionStorage.getItem("dates");
        if(dates !== null){
            dates = $.parseJSON(dates);
            $("div.datepicker-days td.day:not(.disabled)").each(
                function(){
                    var day = $(this).text();
                    $(this).css("background-color", "green");
                    if($.inArray(day, dates.dates) >= 0){
                        $(this).css("background-color", "yellow");
                    }
                    if($.inArray(day, dates.reservedDates) >= 0){
                        $(this).addClass("disabled reserved");
                        $(this).css("background-color", "red");
                    }
                }
            );
        }
    }

    function selectTime(date) {
        if(date.length > 0){
            $.get("'. Url::to('site/times') . '", {date: date}, function (data){
                if(data.length > 0){
                    sessionStorage.setItem("times", data);
                    $("#time").each( function (){ if(this.disabled != null) this.disabled = false; } );
                    $("#time").attr("title", "Выберите время визита к врачу...");
                }
            }
            );
        } else {
            sessionStorage.removeItem("times");
            $("#time").attr("title", "Вы не выбрали дату визита к врачу!");
            $("#time").each( function (){ this.disabled = true; } );
        }
    }

    function showTime(event) {
        $("div.datetimepicker-hours span.reserved").removeClass("disabled");

        var disabledHours = ["0:00", "1:00", "2:00", "3:00", "4:00", "5:00", "6:00", "7:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00"];
        $("div.datetimepicker-hours span.hour").each(
            function(){
                var hour = $(this).text();
                if($.inArray(hour, disabledHours) >= 0){
                    $(this).addClass("disabled");
                }
            }
        );

        $("div.datetimepicker-hours span.hour:not(.disabled)").css("background-color", "green");
        $("div.datetimepicker-hours span.active").removeClass("active");
        $("div.datetimepicker-hours thead tr,th").css("visibility", "hidden");

        var times = sessionStorage.getItem("times");
        if(times !== null){
            times = $.parseJSON(times);

            var reservedHours = [];
            for(var i = 0; i < times.length; i++){
                var hour = times[i].hour;
                reservedHours[i] = hour + ":00";
            }

            $("div.datetimepicker-hours span.hour").each(
                function(){
                    var hour = $(this).text();
                    if($.inArray(hour, reservedHours) >= 0){
                        $(this).addClass("disabled");
                        $(this).css("background-color", "red");
                    }
                }
            );
        }
    }

    function addSubmit(event) {
        if(event.target.value.length > 0){
            $("#submitButton").each( function(){ this.disabled = false; } );
            $("#submitButton").attr("title", "Записаться на приём к врачу...");
        } else {
            $("#submitButton").each( function(){ this.disabled = "disabled"; } );
            $("#submitButton").attr("title", "Вы не заполнили всю форму!");
        }
    }
    ',
    View::POS_END
    );
?>
</div>