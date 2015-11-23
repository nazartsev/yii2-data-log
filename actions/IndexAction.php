<?php

namespace platx\datalog\actions;

use platx\datalog\DataLogForm;
use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;


/**
 * Вывод логов
 * @package platx\datalog\actions
 */
class IndexAction extends Action
{
    /**
     * @var string Файл отображения
     */
    public $viewFile = 'index';

    /**
     * @var int Количество логов на странице
     */
    public $pageSize = 20;

    /**
     */
    public function run()
    {
        /** @var DataLogForm $model */
        $model = new DataLogForm();

        $get = Yii::$app->request->get();

        $dataProvider = $model->search($get);

        $dataProvider->pagination->pageSize = $this->pageSize;

        return Yii::$app->view->render($this->viewFile, [
            'dataProvider' => $dataProvider
        ]);
    }
}