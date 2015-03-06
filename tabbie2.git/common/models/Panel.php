<?php

namespace common\models;

use Exception;
use Yii;

/**
 * This is the model class for table "panel".
 *
 * @property integer $id
 * @property integer $strength
 * @property string $time
 * @property integer $tournament_id
 * @property integer $used
 *
 * @property AdjudicatorInPanel[] $adjudicatorInPanels
 * @property Adjudicator[] $adjudicators
 * @property Debate[] $debates
 * @property Tournament $tournament
 */
class Panel extends \yii\db\ActiveRecord {

    const FUNCTION_CHAIR = 1;
    const FUNCTION_WING = 0;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'panel';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['strength', 'tournament_id', 'used'], 'integer'],
            [['time'], 'safe'],
            [['tournament_id'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'strength' => Yii::t('app', 'Strength'),
            'time' => Yii::t('app', 'Time'),
            'tournament_id' => Yii::t('app', 'Tournament ID'),
            'used' => Yii::t('app', 'Used'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdjudicatorInPanels() {
        return $this->hasMany(AdjudicatorInPanel::className(), ['panel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdjudicators() {
        return $this->hasMany(Adjudicator::className(), ['id' => 'adjudicator_id'])->viaTable('adjudicator_in_panel', ['panel_id' => 'id']);
    }

    /**
     *
     * @param integer $id
     * @return AdjudicatorInPanel
     */
    public function getSpecificAdjudicatorInPanel($id) {
        return AdjudicatorInPanel::findByCondition(["panel_id" => $this->id, "adjudicator_id" => $id])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDebates() {
        return $this->hasMany(Debate::className(), ['panel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament() {
        return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
    }

    public function check() {
        $amount_chairs = 0;
        $amount = 0;
        foreach ($this->adjudicatorInPanels as $adj) {
            if ($adj->function == 1)
                $amount_chairs++;

            if ($adj->adjudicator instanceof Adjudicator)
                $amount++;
        }

        if ($amount > 0 && $amount_chairs == 1)
            return true;
        else
            echo "ID:" . $this->id . " Amount:" . $amount . " & Chairs:" . $amount_chairs . "<br>\n";
        return false;
    }

    /**
     * Gets the Chair in the Panel
     * @return AdjudicatorInPanel
     */
    public function getChairInPanel() {
        return AdjudicatorInPanel::findBySql("SELECT " . AdjudicatorInPanel::tableName() . ".* from " . AdjudicatorInPanel::tableName() . " "
                        . "LEFT JOIN " . Panel::tableName() . " ON panel_id = " . Panel::tableName() . ".id "
                        . "WHERE " . Panel::tableName() . ".id = " . $this->id . " AND " . AdjudicatorInPanel::tableName() . ".function = " . Panel::FUNCTION_CHAIR)->one();
    }

    public function is_chair($id) {
        if ($this->getChairInPanel()->adjudicator_id == $id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sets an ID as Chair in that panel
     * If null, next strongest Adjudicator will be promoted to Chair
     * @param integer|null $id
     */
    public function setChair($id = null) {

        if ($id == null) {
            $nextHighestAdj = AdjudicatorInPanel::find()->where([
                        "panel_id" => $this->id
                    ])->joinWith("adjudicator")->orderBy("strength")->one();
            $id = $nextHighestAdj->adjudicator_id;
        }

        $oldChair = $this->getChairInPanel();
        if ($oldChair instanceof AdjudicatorInPanel) {
            $oldChair->function = Panel::FUNCTION_WING;
            $oldChair->save();
        }
        $chair = $this->getSpecificAdjudicatorInPanel($id);
        $chair->function = Panel::FUNCTION_CHAIR;

        if ($chair->save())
            return true;
        else
            return $chair->getErrors();
    }

    /**
     * Changes the Panel of the ID
     * @param Panel $newPanel
     * @param integer $id
     */
    public function changeTo($newPanel, $id) {
        $adj = $this->getSpecificAdjudicatorInPanel($id);
        if ($adj instanceof AdjudicatorInPanel) {
            $adj->panel_id = $newPanel->id;
            if ($adj->save())
                return true;
            else
                return $adj->getErrors();
        } else
            throw new Exception("getSpecificAdjudicatorInPanel with ID " . $id . " NOT found");
    }

    public function setWing($id) {
        $adj = $this->getSpecificAdjudicatorInPanel($id);

        if ($adj->function == Panel::FUNCTION_CHAIR) {
            $nextHighestAdjNotID = AdjudicatorInPanel::find()
                            ->where("panel_id = " . $this->id . " AND adjudicator_id != " . $id)
                            ->joinWith("adjudicator")->orderBy("strength")->one();
            $id = $nextHighestAdjNotID->adjudicator_id;
            $this->setChair($id);
        }
        //Read again if save has been done
        $adj->refresh();
        if ($adj->function == Panel::FUNCTION_WING)
            return true;
        else
            return false;
    }

    public function setAllWings() {
        foreach ($this->adjudicatorInPanels as $adj) {
            $adj->function = Panel::FUNCTION_WING;
            $adj->save();
        }
        return true;
    }

}
