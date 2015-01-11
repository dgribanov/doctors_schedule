<?php
/* @var $this yii\web\View */
$this->title = 'Запись на приём к врачу';
?>

<?php
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\Button;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;
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
                            'onchange' => 'selectDocSpec(this.id)'
                        ]
                    );
                ?>
                <?= $form->field($patient, 'firstname')->textInput(
                        [
                            'id'=>'patient-firstname',
                            'class' => 'patient-info',
                            'onchange' => 'selectDocSpec(this.id)'
                        ]
                    );
                ?>
                <?= $form->field($patient, 'patronymic')->textInput( 
                        [
                            'id'=>'patient-patronymic',
                            'class' => 'patient-info',
                            'onchange' => 'selectDocSpec(this.id)'
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
                            'title' => 'Вы не завершили ввод личных данных!',
                            'disabled' => 'disabled',
                            'onchange' => 'selectDoctor(this.value, this.id)'
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
                            'onchange' => 'selectDate(this.value, this.id)'
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
                                'onchange' => 'selectTime(this.value, this.id)'
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

        <div class="buttons-group">

        <?php
        echo Html::a('Записаться на приём', '#', [
                    'id' => 'submit-button', 
                    'class' => 'btn btn-primary', 
                    'onclick' => 'submitForm()', 
                    'disabled' => 'disabled', 
                    'title' => 'Вы не заполнили всю форму!'
                ]
            );
        ?>

        <?php
            echo Button::widget([
                'id' => 'reset-button',
                'label' => 'Очистить форму',
                'options' => [
                    'class' => 'btn btn-primary',
                    'onclick' => 'resetForm()'
                ],
            ]);
        ?>

        </div>

        <?php
            ActiveForm::end();
        ?>

    </div>

<?php
    $this->registerJs('
    function selectDocSpec(elemId) {
        var values = $(".patient-info").map(function(){return this.value;});
        if($.inArray("", values) < 0) {
            $("#doctor-spec").each( function (){ this.disabled = false; } );
            $("#doctor-spec").attr("title", "Выберите специализацию врача...");
            $("#doctors-header, #doc-spec-label").removeClass("not-active-field");
        } else {
            resetForm(elemId);
        }
    }

    function selectDoctor(id, elemId) {
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
            resetForm(elemId);
        }
    }

    function selectDate(id, elemId) {
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
            resetForm(elemId);
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

    function selectTime(date, elemId) {
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
            resetForm(elemId);
        }
    }

    function showTime(event) {
        $("div.datetimepicker-hours span").removeClass("active disabled reserved available");
        $("div.datetimepicker-hours thead tr,th").css("visibility", "hidden");

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
            $("#submit-button").removeAttr("disabled");
            $("#submit-button").attr("title", "Записаться на приём к врачу...");
        } else {
            $("#submit-button").attr("disabled", "disabled");
            $("#submit-button").attr("title", "Вы не заполнили всю форму!");
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

    function submitForm() {
        var firstname = $("#patient-firstname").val();
        var surname = $("#patient-surname").val();
        var patronymic = $("#patient-patronymic").val();
        var doctorId = $("#doctors").val();
        var date = $("#dates").val();
        var time = $("#time").val();
        $.post("'. Url::to('site/save') . '",
            {firstname: firstname, surname: surname, patronymic: patronymic, doctorId: doctorId, date: date, time: time},
            function (data){
                resetForm();
                showErrors(data);
            }
        );
    }

    function resetForm(elemId) {
        if(elemId.length > 0){
            switch (elemId) {
                case "patient-surname":
                case "patient-firstname":
                case "patient-patronymic":
                    document.dates = document.times = null;
                    $("#doctor-spec, #doctors, #dates, #time, #submit-button").attr("disabled", "disabled");
                    $("#doctor-spec, #doctors, #dates, #time").val("");
                    $("#doctors-header, #doc-spec-label, #doctors-label, #date-header, #date-label, #time-header, #time-label").addClass("not-active-field");
                    $("#doctor-spec").attr("title", "Вы не завершили ввод личных данных!");
                    $("#doctors").attr("title", "Вы не выбрали специализацию врача!");
                    $("#dates").attr("title", "Вы не выбрали врача!");
                    $("#time").attr("title", "Вы не выбрали дату визита к врачу!");
                    $("#submit-button").attr("title", "Вы не заполнили всю форму!");
                    $("#doctors > option:not(:first-child)").remove();
                    break;
                case "doctor-spec":
                    document.dates = document.times = null;
                    $("#doctors, #dates, #time, #submit-button").attr("disabled", "disabled");
                    $("#doctors, #dates, #time").val("");
                    $("#doctors-label, #date-header, #date-label, #time-header, #time-label").addClass("not-active-field");
                    $("#doctors").attr("title", "Вы не выбрали специализацию врача!");
                    $("#dates").attr("title", "Вы не выбрали врача!");
                    $("#time").attr("title", "Вы не выбрали дату визита к врачу!");
                    $("#submit-button").attr("title", "Вы не заполнили всю форму!");
                    $("#doctors > option:not(:first-child)").remove();
                    break;
                case "doctors":
                    document.dates = document.times = null;
                    $("#dates, #time, #submit-button").attr("disabled", "disabled");
                    $("#dates, #time").val("");
                    $("#date-header, #date-label, #time-header, #time-label").addClass("not-active-field");
                    $("#dates").attr("title", "Вы не выбрали врача!");
                    $("#time").attr("title", "Вы не выбрали дату визита к врачу!");
                    $("#submit-button").attr("title", "Вы не заполнили всю форму!");
                    break;
                case "dates":
                    document.times = null;
                    $("#time, #submit-button").attr("disabled", "disabled");
                    $("#time").val("");
                    $("#time-header, #time-label").addClass("not-active-field");
                    $("#time").attr("title", "Вы не выбрали дату визита к врачу!");
                    $("#submit-button").attr("title", "Вы не заполнили всю форму!");
                    break;
            }
            $("div.datetimepicker-hours span").removeClass("active disabled reserved available");
        } else {
            $("#active-form").trigger("reset");
            $("#doctor-spec, #doctors, #dates, #time, #submit-button").attr("disabled", "disabled");
            $("div.datetimepicker-hours span").removeClass("active disabled reserved available");
            $("#doctors-header, #doc-spec-label, #doctors-label, #date-header, #date-label, #time-header, #time-label").addClass("not-active-field");
            $("#doctor-spec").attr("title", "Вы не завершили ввод личных данных!");
            $("#doctors").attr("title", "Вы не выбрали специализацию врача!");
            $("#dates").attr("title", "Вы не выбрали врача!");
            $("#time").attr("title", "Вы не выбрали дату визита к врачу!");
        }
    }
    ',
    View::POS_END
    );
?>
</div>