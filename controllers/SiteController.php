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
            $date = date('Y-m-d', strtotime($date));
            $result = Schedule::find()->select('EXTRACT(HOUR FROM time) AS hour')->where(['date' => $date])->asArray()->all();
        }

        return Json::encode($result);
    }

    public function actionSave()
    {
        $result = [];
        if(isset($_POST['date']) && !empty($_POST['date'])){

        }

        return Json::encode($result);
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
