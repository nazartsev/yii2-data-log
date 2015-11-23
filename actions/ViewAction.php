<?php

namespace platx\datalog\actions;

use platx\datalog\DataLogForm;
use Yii;
use yii\web\NotFoundHttpException;


/**
 * Просмотр лога
 * @package platx\datalog\actions
 */
class ViewAction
{
    /**
     * @var string Файл отображения
     */
    public $viewFile = 'view';

    /**
     * @param $id
     * @return $this|bool
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        /** @var DataLogForm $dataLogForm */
        $dataLogForm = DataLogForm::findOne(['id' => $id]);

        if (empty($dataLogForm)) {
            throw new NotFoundHttpException('Лог не найден!');
        }

        return Yii::$app->view->render($this->viewFile, [
            'model' => $dataLogForm
        ]);
    }
}