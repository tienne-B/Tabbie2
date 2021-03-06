<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "energy_config".
 *
 * @property integer    $id
 * @property string     $key
 * @property integer    $tournament_id
 * @property string     $label
 * @property integer    $value
 * @property Tournament $tournament
 */
class EnergyConfig extends \yii\db\ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'energy_config';
	}

	public static function get($key, $tournament_id) {
		$conf = EnergyConfig::findByCondition([
			"key"           => $key,
			"tournament_id" => $tournament_id,
		])->one();
		if ($conf) {
			return $conf->value;
		} else {
			return null;
		}
	}

	public static function loadArray($tournament_id) {
		$config = [];
		$en = EnergyConfig::find()->tournament($tournament_id)->asArray()->all();

		return ArrayHelper::map($en, "key", "value");
	}

	/**
	 * @inheritdoc
	 * @return TournamentQuery
	 */
	public static function find()
	{
		return new TournamentQuery(get_called_class());
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['key', 'tournament_id', 'label'], 'required'],
			[['tournament_id', 'value'], 'integer'],
			[['key'], 'string', 'max' => 100],
			[['label'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id'            => Yii::t('app', 'ID'),
			'key'           => Yii::t('app', 'Key'),
			'tournament_id' => Yii::t('app', 'Tournament') . ' ' . Yii::t('app', 'ID'),
			'label'         => Yii::t('app', 'Label'),
			'value'         => Yii::t('app', 'Value'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTournament()
	{
		return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
	}

	/**
	 * @param Tournament $tournament
	 *
	 * @return bool
	 */
	public function setup($tournament)
	{
		$algo = $tournament->getTabAlgorithmInstance();
		if ($algo->setup($tournament))
			return true;
		else
			return false;
	}

}
