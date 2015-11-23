<?php

namespace platx\datalog\actions;

use platx\datalog\DataLogForm;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;


/**
 * Удаление лога
 * @package platx\datalog\actions
 */
class DeleteAction extends Action
{
    /** @var array $redirectUrl Редирект */
    public $redirectUrl = ['index'];

    /**
     * @param $id
     * @return $this
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function run($id)
    {
        /** @var DataLogForm $dataLogForm */
        $dataLogForm = DataLogForm::findOne(['id' => $id]);

        if (empty($dataLogForm)) {
            throw new NotFoundHttpException('Лог не найден!');
        }

        if ($dataLogForm->delete()) {
            Yii::$app->session->setFlash('success');
        } else {
            Yii::$app->session->setFlash('error');
        }

        if(!Yii::$app->request->isAjax) {
            return $this->controller->redirect($this->redirectUrl);
        }

        return true;
    }
}