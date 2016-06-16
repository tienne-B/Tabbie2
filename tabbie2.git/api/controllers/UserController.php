<?php
/**
 * MotionController.php File
 *
 * @package     Tabbie2
 * @author      jareiter
 * @version     1
 */

namespace api\controllers;


use api\models\User;

/**
 * Class UserController
 * @package api\controllers
 */
class UserController extends BaseRestController
{
	/**
	 * @inheritdoc
	 */
	public $modelClass = 'api\models\User';

	/**
	 * Return the allowed action for this object
	 * @return array
	 */
	public function actions()
	{
		$actions = parent::actions();

		// disable the "delete" and "create" actions
		unset($actions['delete'], $actions['index'], $actions['create'], $actions['update']);

		return $actions;
	}

	/**
	 * Returns the self Identity
	 * @return null|static
	 */
	public function actionMe()
	{
		return User::findIdentityByAccessToken(\Yii::$app->request->get("access-token"));
	}
}