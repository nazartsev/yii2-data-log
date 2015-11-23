<?php

namespace platx\datalog\actions;

use platx\datalog\DataLogForm;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;


/**
 * Изменение записи по логам
 * @package platx\datalog\actions
 */
class RollbackAction extends Action
{
    /** @var array $redirectUrl Редирект */
    public $redirectUrl = ['index'];

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

        if ($dataLogForm->rollback()) {
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