<?php

namespace platx\datalog\actions;

use platx\datalog\DataLogForm;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;


/**
 * Просмотр лога
 * @package platx\datalog\actions
 */
class ViewAction extends Action
{
    /**
     * @var string Файл отображения
     */
    public $viewFile;

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

        return $this->controller->render($this->viewFile, [
            'model' => $dataLogForm
        ]);
    }
}