<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "adjudicator_in_panel".
 *
 * @property integer     $adjudicator_id
 * @property integer     $panel_id
 * @property integer     $function
 * @property Adjudicator $adjudicator
 * @property Panel       $panel
 */
class AdjudicatorInPanel extends \yii\db\ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'adjudicator_in_panel';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['adjudicator_id', 'panel_id'], 'required'],
			[['adjudicator_id', 'panel_id', 'function'], 'integer']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'adjudicator_id' => Yii::t('app', 'Adjudicator') . ' ' . Yii::t('app', 'ID'),
			'panel_id'       => Yii::t('app', 'Panel') . ' ' . Yii::t('app', 'ID'),
			'function'       => Yii::t('app', 'Function'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getAdjudicator()
	{
		return $this->hasOne(Adjudicator::className(), ['id' => 'adjudicator_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getPanel()
	{
		return $this->hasOne(Panel::className(), ['id' => 'panel_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDebate() {
		return $this->hasMany(Debate::className(), ['panel_id' => 'panel_id']);
	}

	public function is_chair()
	{
		if ($this->function == Panel::FUNCTION_CHAIR)
			return true;
		else
			return false;
	}

}
