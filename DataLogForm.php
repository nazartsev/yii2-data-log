<?php

namespace platx\datalog;

use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;


/**
 * Форма для поиска логов и изменения записей по логам
 * @package platx\datalog
 */
class DataLogForm extends DataLog
{
    /**
     * Конструктор запросов
     * @var null|ActiveQuery
     */
    protected $_mainQuery;

    /**
     * Откат записи на предыдущее состояние
     * @return bool Результат отката
     */
    public function rollback()
    {
        $result = false;

        switch ($this->type_key) {
            case self::TYPE_INSERT :
                $result = $this->rollbackDelete();
                break;
            case self::TYPE_UPDATE :
                $result = $this->rollbackUpdate();
                break;
            case self::TYPE_DELETE :
                $result = $this->rollbackCreate();
                break;
        }

        return $result;
    }

    /**
     * Создание записи по логам
     * @return bool
     */
    public function rollbackCreate()
    {
        $modelName = "\\{$this->model_name}";

        /** @var ActiveRecord $model */
        $model = new $modelName;

        $model->setAttributes($this->dataPrev);

        if($model->save()) {
            return true;
        }

        return false;
    }

    /**
     * Изменение записи по логам
     * @return bool
     */
    public function rollbackUpdate()
    {
        $modelName = "\\{$this->model_name}";

        /** @var ActiveRecord $model */
        $model = new $modelName;

        $model = $model::findOne($this->primaryKey);

        if(!empty($model)) {
            $model->setAttributes($this->dataPrev);

            if ($model->save()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Удаление записи по логам
     * @return bool
     * @throws \Exception
     */
    public function rollbackDelete()
    {
        $modelName = "\\{$this->model_name}";

        /** @var ActiveRecord $model */
        $model = new $modelName;

        $model = $model::findOne($this->recordId);

        if(!empty($model)) {
            if ($model->delete()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Поиск моделей
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->_mainQuery = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $this->_mainQuery,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ]
        ]);

        if(!$this->load($params)) {
            return $dataProvider;
        }

        $this->filterAttributes();

        return $dataProvider;
    }

    /**
     * Построение запроса на выборку
     * @return null|ActiveQuery Конструктор запроса
     */
    public function buildQuery()
    {
        $this->_mainQuery = self::find();

        $this->filterAttributes();

        return $this->_mainQuery;
    }

    /**
     * Фильтрация по атрибутам
     */
    protected function filterAttributes()
    {
        if($this->_mainQuery) {
            if($this->hasAttribute('id')) {
                $this->_mainQuery->andFilterWhere(['id' => $this->id]);
            }
            if($this->hasAttribute('user_id')) {
                $this->_mainQuery->andFilterWhere(['user_id' => $this->user_id]);
            }
            if($this->hasAttribute('type_key')) {
                $this->_mainQuery->andFilterWhere(['type_key' => $this->type_key]);
            }
            if($this->hasAttribute('app_id')) {
                $this->_mainQuery->andFilterWhere(['app_id' => $this->app_id]);
            }
            if($this->hasAttribute('model_name')) {
                $this->_mainQuery->andFilterWhere(['like', 'model_name', $this->model_name]);
            }
            if($this->hasAttribute('model_name')) {
                $this->_mainQuery->andFilterWhere(['like', 'table_name', $this->table_name]);
            }
            if($this->hasAttribute('created_at')) {
                $this->_mainQuery->andFilterWhere(['like', 'created_at', $this->created_at]);
            }
        }
    }
}