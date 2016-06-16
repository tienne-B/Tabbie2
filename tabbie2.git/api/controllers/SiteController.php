<?php
namespace api\controllers;

use api\models\ApiUser;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;

/**
 * Site controller
 */
class SiteController extends Controller
{
	/**
	 * @return array
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions' => ['index', 'login', 'error'],
						'allow' => true,
					],
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
					[
						'actions' => [],
						'allow' => true,
						'matchCallback' => function ($rule, $action) {
							return (Yii::$app->user->isAdmin());
						}
					],
				],
			],
		];
	}

	/**
	 * Return the allowed action for this object
	 * @return array
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

	/**
	 * @return string
	 */
	public function actionIndex()
	{
		return $this->render('index');
	}

	/**
	 * @return string|\yii\web\Response
	 */
	public function actionLogin()
	{
		if (!\Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login()) {

			if(count(Yii::$app->user->identity->apiUser) == 0) {
				$api = new ApiUser([
					"user_id" => Yii::$app->user->id,
					"access_token" => Yii::$app->getSecurity()->generateRandomString(20),
				]);
				$api->save();

				return $this->redirect("profile");
			}
		} else {
			return $this->render('login', [
				'model' => $model,
			]);
		}
	}

	/**
	 * @return \yii\web\Response
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout();

		return $this->goHome();
	}

	/**
	 * @return string
	 */
	public function actionProfile(){
		$model = Yii::$app->user->identity;

		return $this->render('profile', [
				'model' => $model,
		]);
	}
}
