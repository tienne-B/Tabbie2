<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\ImportForm;

/* @var $this yii\web\View */
/* @var $model ImportForm */

$this->title = Yii::t('app', 'Import {modelClass}', [
    'modelClass' => 'Team',
]);
$tournament = $this->context->_getContext();
$this->params['breadcrumbs'][] = ['label' => $tournament->fullname, 'url' => ['tournament/view', "id" => $tournament->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teams'), 'url' => ['index', 'tournament_id' => $tournament->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="team-import">

        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(['options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'loading'
        ]]); ?>

        <? if (isset($model->tempImport)): ?>
            <table class="table import-table">
                <tr>
                    <th><?= Yii::t("app", "Team Name") ?></th>
                    <th><?= Yii::t("app", "Society") ?></th>
                    <th><?= Yii::t("app", "Speaker A") ?></th>
                    <th><?= Yii::t("app", "Speaker B") ?></th>
                    <? for ($i = $min_columns; $i < count($model->header); $i++): ?>
                        <th><?= $model->header[$i] ?></th>
                    <? endfor; ?>
                </tr>
                <? for ($i = 1; $i <= count($model->tempImport); $i++): ?>
                    <? if (!isset($model->tempImport[$i])) continue; ?>
                    <tr>
                        <td>
                            <? echo $model->tempImport[$i][0][0] //Team name - never a problem           ?>
                        </td>
                        <?
                        $societyField = $model->tempImport[$i][1];
                        $options = [];
                        if (count($societyField) == 1) { //NEW
                            $class = "new";
                            $value = $societyField[0];
                        } else if (count($societyField) == 2) { //Found 1 - easy
                            $class = "green";
                            $value = Html::a($societyField[1]["name"], ["society/view", "id" => $societyField[1]["id"]]);
                        } else { //Ups found multiple
                            $class = "yellow";
                            $options = [];
                            for ($a = 1; $a < count($societyField); $a++) {
                                $options[$societyField[$a]["id"]] = $societyField[$a]["name"];
                            }
                        }
                        ?>
                        <td class="<?= $class ?>">
                            <?
                            if ($class == "green" OR $class == "new") {
                                echo $value;
                            } else {
                                echo $model->tempImport[$i][1][0] . "<br>";
                                echo Html::dropDownList("field[$i][1]", $societyField[0], $options);
                            }
                            ?>
                        </td>

                        <?
                        $userField = $model->tempImport[$i][2];
                        $class = "none";
                        $value = "#";
                        $options = [];

                        if (count($userField) == 1) { //NEW
                            $class = "new";
                            $value = $userField[0] . " " . $model->tempImport[$i][3][0];
                        } else if (count($userField) == 2) { //Found 1 - easy
                            $class = "green";
                            $value = Html::a($userField[1]["name"], ["user/view", "id" => $userField[1]["id"]]);
                        } else { //Ups found multiple
                            $class = "yellow";
                            for ($a = 1; $a < count($userField); $a++) {
                                $options[$userField[$a]["id"]] = $userField[$a]["name"] . " (" . $userField[$a]["email"] . ")";
                            }
                        }
                        ?>
                        <td class="<?= $class ?>">
                            <?
                            if ($class == "green" OR $class == "new" OR $class == "none") {
                                echo $value;
                            } else {
                                echo $model->tempImport[$i][2][0] . " " . $model->tempImport[$i][3][0] . " (" . $model->tempImport[$i][4][0] . ")<br>";
                                echo Html::dropDownList("field[$i][2]", $userField[0], $options);
                            }
                            ?>
                        </td>

                        <?
                        $userField = $model->tempImport[$i][5];
                        $class = "none";
                        $value = "#";
                        $options = [];

                        if (count($userField) == 1) { //NEW
                            $class = "new";
                            $value = $userField[0] . " " . $model->tempImport[$i][6][0];
                        } else if (count($userField) == 2) { //Found 1 - easy
                            $class = "green";
                            $value = Html::a($userField[1]["name"], ["user/view", "id" => $userField[1]["id"]]);
                        } else { //Ups found multiple
                            $class = "yellow";
                            for ($a = 1; $a < count($userField); $a++) {
                                $options[$userField[$a]["id"]] = $userField[$a]["name"] . " (" . $userField[$a]["email"] . ")";
                            }
                        }
                        ?>

                        <td class="<?= $class ?>">
                            <?
                            if ($class == "green" OR $class == "new" OR $class == "none") {
                                echo $value;
                            } else {
                                echo $model->tempImport[$i][5][0] . " " . $model->tempImport[$i][6][0] . " (" . $model->tempImport[$i][7][0] . ")<br>";
                                echo Html::dropDownList("field[$i][5]", $userField[0], $options);
                            }
                            ?>
                        </td>

                        <? for ($c = $min_columns; $c < count($model->header); $c++): ?>
                            <td><? echo isset($model->tempImport[$i][$c]) ? $model->tempImport[$i][$c][0] : "" ?></td>
                        <? endfor; ?>

                    </tr>
                <? endfor; ?>
            </table>
            <div class="form-group">
                <?= Html::hiddenInput("csvFile", serialize($model->tempImport)); ?>
                <?= Html::hiddenInput("header", serialize($model->header)); ?>
                <?= Html::hiddenInput("makeItSo", "true"); ?>
                <?= Html::submitButton(Yii::t('app', 'Make it so'), ['class' => 'btn btn-block btn-success'])
                ?>
            </div>

        <? else: ?>
            <div class="team-form">
                <div class="row">
                    <div class="col-xs-12">
                        <?=
                        $form->field($model, 'csvFile')->fileInput([
                            'accept' => '.csv'
                        ])
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <?= $form->field($model, 'delimiter')->dropDownList(\frontend\models\ImportForm::getDelimiterOptions()); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <?= $form->field($model, 'send_mail_option')->checkbox(); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Import'), ['class' => 'btn btn-success']) ?>
                </div>


            </div>
        <? endif; ?>
    </div>
<?php ActiveForm::end(); ?>