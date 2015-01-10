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

    <div class="body-content">

        <div id="alert-errors"></div>

        <?php
            $form = ActiveForm::begin([
                'id' => 'active-form',
                'type' => ActiveForm::TYPE_VERTICAL
            ])
        ?>

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
            <div class="col-lg-4">
                <h2 id="doctors-header" class="not-active-field">2. Выберите врача:</h2>

                <?= $form->field($doctors, 'specialization', [
                    'labelOptions' => [
                        'id' => 'doc-spec-label',
                        'class'=>'not-active-field'
                        ]
                    ] )->dropDownList(
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
                <?= $form->field($doctors, 'name', [
                    'labelOptions' => [
                        'id' => 'doctors-label',
                        'class'=>'not-active-field'
                        ]
                    ] )->dropDownList( 
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
            <div class="col-lg-4">
                <h2 id="date-header" class="not-active-field">3. Выберите дату визита:</h2>

                <?php
                    echo '<label id="date-label" class="control-label not-active-field">Дата визита</label>';
                    echo DatePicker::widget([
                            'name' => 'date',
                            'type' => DatePicker::TYPE_INPUT,
                            'value' => '',
                            'readonly' => true,
                            'options' => [
                                'id'=>'dates',
                                'placeholder' => 'Выберите дату визита к врачу...',
                                'title' => 'Вы не выбрали врача!',
                                'disabled' => 'disabled',
                                'onchange' => 'selectTime(this.value)'
                            ],
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                                'daysOfWeekDisabled' => [0,6],
                                'startDate' => date('Y-m-d', strtotime("tomorrow")),
                                'endDate' => date('Y-m-d', strtotime("+3 week"))
                            ],
                            'pluginEvents' => [
                                'show' => 'showDates'
                                
                            ]
                        ]
                    );
                ?>

                <p>Можно записаться на ближайшие три недели.</p>
            </div>
            <div class="col-lg-4">
                <h2 id="time-header" class="not-active-field">4. Выберите время:</h2>

                <?php
                    echo '<label id="time-label" class="control-label not-active-field">Время визита</label>';
                    echo DateTimePicker::widget([
                        'name' => 'time',
                        'type' => DateTimePicker::TYPE_INPUT,
                        'value' => '',
                        'pickerButton' => false,
                        'readonly' => true,
                        'options' => [
                            'id'=>'time',
                            'placeholder' => 'Выберите время визита к врачу...',
                            'title' => 'Вы не выбрали дату визита к врачу!',
                            'disabled' => 'disabled'
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
                    showErrors(data);
                }'),
            ],
            'options' => [
                'type' => 'submit',
                'id' => 'submitButton',
                'disabled' => 'disabled',
                'title' => 'Вы не заполнили всю форму!'
            ],
        ]
        );

        AjaxSubmitButton::end();
        ?>

        </div>

        <?php
            ActiveForm::end();
        ?>

    </div>

<?php
    $this->registerJs('
    function selectDocSpec() {
        var values = $(".patient-info").map(function(){return this.value;});
        if($.inArray("", values) < 0) {
            $("#doctor-spec").each( function (){ this.disabled = false; } );
            $("#doctors-header, #doc-spec-label").removeClass("not-active-field");
        } else {
            $("#doctor-spec").each( function (){ this.disabled = "disabled"; } );
            $("#doctors-header, #doc-spec-label").addClass("not-active-field");
        }
    }

    function selectDoctor(id) {
        if(id.length > 0){
            $.get("'. Url::to('site/doctors') . '", {id: id}, function (data){
                if(data.length > 0){
                    document.dates = null;
                    var doctors = $.parseJSON(data);
                    $("#doctors").each( function (){ this.disabled = false; } );
                    $("#doctors > option:not(:first-child)").remove();
                    $("#doctors").attr("title", "Выберите врача...");
                    $("#doctors-label").removeClass("not-active-field");
                    for(var i = 0; i < doctors.length; i++){
                        $("#doctors").append(
                            $("<option />", {value: doctors[i].id}).text(doctors[i].surname + " " + doctors[i].firstname + " " + doctors[i].patronymic)
                        );
                    }
                }
            }
            );
        } else {
            document.dates = null;
            $("#doctors > option:not(:first-child)").remove();
            $("#doctors").attr("title", "Вы не выбрали специализацию врача!");
            $("#doctors").each( function (){ this.disabled = "disabled"; } );
            $("#doctors-label").addClass("not-active-field");
        }
    }

    function selectDate(id) {
        if(id.length > 0){
            $.get("'. Url::to('site/dates') . '", {id: id}, function (data){
                if(data.length > 0){
                    document.dates = data;
                    $("#dates").each( function (){ this.disabled = false; } );
                    $("#dates").attr("title", "Выберите дату визита к врачу...");
                    $("#date-header, #date-label").removeClass("not-active-field");
                }
            }
            );
        } else {
            document.dates = null;
            $("#dates").attr("title", "Вы не выбрали врача!");
            $("#dates").each( function (){ this.disabled = "disabled"; } );
            $("#date-header, #date-label").addClass("not-active-field");
        }
    }

    function showDates(event) {
        $("div.datepicker-days td.active").removeClass("active");
        $("div.datepicker-days td.reserved").removeClass("disabled reserved available partly-available");
        var dates = document.dates;
        if(dates !== null){
            dates = $.parseJSON(dates);
            $("div.datepicker-days td.day:not(.disabled)").each(
                function(){
                    var day = $(this).text();
                    $(this).addClass("available");
                    $(this).attr("title", "Зелёным цветом отмечены доступные для записи даты");
                    if($.inArray(day, dates.dates) >= 0){
                        $(this).removeClass("available").addClass("partly-available");
                        $(this).attr("title", "Жёлтым цветом отмечены даты частично доступные для записи");
                    }
                    if($.inArray(day, dates.reservedDates) >= 0){
                        $(this).removeClass("available").addClass("disabled reserved");
                        $(this).attr("title", "Красным цветом отмечены даты недостуные для записи");
                    }
                }
            );
        }
    }

    function selectTime(date) {
        if(date.length > 0){
            $.get("'. Url::to('site/times') . '", {date: date}, function (data){
                if(data.length > 0){
                    document.times = data;
                    $("#time").each( function (){ this.disabled = false; } );
                    $("#time").attr("title", "Выберите время визита к врачу...");
                    $("#time-header, #time-label").removeClass("not-active-field");
                }
            }
            );
        } else {
            document.times = null;
            $("#time").attr("title", "Вы не выбрали дату визита к врачу!");
            $("#time").each( function (){ this.disabled = "disabled"; } );
            $("#time-header, #time-label").addClass("not-active-field");
        }
    }

    function showTime(event) {
        $("div.datetimepicker-hours span.active").removeClass("active");
        $("div.datetimepicker-hours thead tr,th").css("visibility", "hidden");
        $("div.datetimepicker-hours span.reserved").removeClass("disabled reserved available");

        var disabledHours = ["0:00", "1:00", "2:00", "3:00", "4:00", "5:00", "6:00", "7:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00"];
        $("div.datetimepicker-hours span.hour").each(
            function(){
                var hour = $(this).text();
                if($.inArray(hour, disabledHours) >= 0){
                    $(this).addClass("disabled");
                }
            }
        );

        $("div.datetimepicker-hours span.hour:not(.disabled)").addClass("available").attr("title", "Зелёным цветом отмечены доступное для записи время");

        var times = document.times;
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
                        $(this).removeClass("available").addClass("disabled reserved");
                        $(this).attr("title", "Красным цветом отмечено время недостуное для записи");
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

    function showErrors(errors) {
        errors = $.parseJSON(errors);
        $("#alert-errors").empty();
        if(errors.length > 0){
            for(var i = 0; i < errors.length; i++){
                $("#alert-errors").append( $("<div />", {class: "error-summary"}).text(errors[i]) );
            }
        } else {
            $("#alert-errors").append( $("<div />", {class: "success-summary"}).text("Вы записаны на приём к врачу!") );
        }
    }
    ',
    View::POS_END
    );
?>
</div>