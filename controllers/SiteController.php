<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\Doctors;
use app\models\DoctorSpecialization;
use app\models\LoginForm;
use app\models\Schedule;
use app\models\Patients;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex() {
        $doctorSpec = DoctorSpecialization::find()->where('id IN (SELECT DISTINCT specialization FROM doctors)')->all();
        $schedule = new Schedule();
        $doctors = new Doctors();
        $patient = new Patients();

        return $this->render('index', ['doctors' => $doctors, 'patient' => $patient, 'schedule' => $schedule, 'doctorSpec' => $doctorSpec]);
    }

    public function actionDoctors()
    {
        $result = [];
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $_GET['id'];
            $result = Doctors::find()->select('id, firstname, surname, patronymic')->where(['status' => Doctors::STATUS_ACTIVE, 'specialization' => $id])->all();
        }

        return Json::encode($result);
    }

    public function actionDates()
    {
        $result = [];
        if(isset($_GET['id']) && !empty($_GET['id'])){
            $id = $_GET['id'];
            $dates = Schedule::find()->select('EXTRACT(DAY FROM date) AS date')->where(['doctor_id' => $id])->groupBy('date')->having('COUNT(*) < 18')->asArray()->all();
            $reservedDates = Schedule::find()->select('EXTRACT(DAY FROM date) AS date')->where(['doctor_id' => $id])->groupBy('date')->having('COUNT(*) = 18')->asArray()->all();
            if(!empty($dates)){
                $result['dates'] = [];
                foreach ($dates as $date) {
                    array_push($result['dates'], $date['date']);
                }
            }
            if(!empty($reservedDates)){
                $result['reservedDates'] = [];
                foreach ($reservedDates as $date) {
                    array_push($result['reservedDates'], $date['date']);
                }
            }
        }

        return Json::encode($result);
    }

    public function actionTimes()
    {
        $result = [];
        if(isset($_GET['date']) && !empty($_GET['date'])){
            $date = $_GET['date'];
            $result = Schedule::find()->select('EXTRACT(HOUR FROM time) AS hour')->where(['date' => $date])->asArray()->all();
        }

        return Json::encode($result);
    }

    public function actionSave()
    {
        $errors = [];
        if(isset($_POST['doctorId']) && !empty($_POST['doctorId'])){
            $doctorId = Yii::$app->request->post('doctorId');
            $doctor = Doctors::find()->where(['id' => $doctorId])->one();
            if(empty($doctor)){
                $errors[] = 'Врач не найден!';
            }
        } else {
            $errors[] = 'Вы не выбрали врача!';
        }

        if(count($errors) == 0){
            if(isset($_POST['firstname']) && !empty($_POST['firstname']) && isset($_POST['surname']) && !empty($_POST['surname']) && isset($_POST['patronymic']) && !empty($_POST['patronymic'])){
                $firstname = Yii::$app->request->post('firstname');
                $surname = Yii::$app->request->post('surname');
                $patronymic = Yii::$app->request->post('patronymic');
                $patient = Patients::find()->where(['firstname' => $firstname, 'surname' => $surname, 'patronymic' => $patronymic])->one();
                if(empty($patient)) {
                    $patient = new Patients();
                    $patient->firstname = $firstname;
                    $patient->surname = $surname;
                    $patient->patronymic = $patronymic;
                    if( !($patient->save()) ) {
                        $errors[] = 'Ошибка при сохранении данных пациента!';
                    }
                }
            } else {
                $errors[] = 'Вы не ввели все данные о себе!';
            }
        }

        if(count($errors) == 0){
            if(isset($_POST['date']) && !empty($_POST['date']) && isset($_POST['time']) && !empty($_POST['time'])){
                $requestDate = Yii::$app->request->post('date');
                $requestTime = Yii::$app->request->post('time');
                $date = mysql_escape_string($requestDate);
                $time = mysql_escape_string($requestTime);
                $schedule = new Schedule();
                $schedule->doctor_id = $doctor->id;
                $schedule->patient_id = $patient->id;
                $schedule->date = $date;
                $schedule->time = $time;
                if( !($schedule->save()) ){
                    $errors[] = 'Ошибка при сохранении данных в расписание приёма врачей!';
                }
            } else {
                $errors[] = 'Вы не указали дату или время приёма!';
            }
        }

        return Json::encode($errors);
    }

//    public function actionLogin() {
//        if (!\Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        } else {
//            return $this->render('login', [
//                        'model' => $model,
//            ]);
//        }
//    }

//    public function actionLogout() {
//        Yii::$app->user->logout();
//
//        return $this->goHome();
//    }
//
//    public function actionContact() {
//        $model = new ContactForm();
//        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
//            Yii::$app->session->setFlash('contactFormSubmitted');
//
//            return $this->refresh();
//        } else {
//            return $this->render('contact', [
//                        'model' => $model,
//            ]);
//        }
//    }

    public function actionAbout() {
        return $this->render('about');
    }

}
