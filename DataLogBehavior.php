<?php

namespace platx\datalog;

use yii\db\ActiveRecord;
use Yii;
use yii\base\Behavior;


/**
 * Запись изменений в логи
 * @property ActiveRecord $owner
 * @package platx\datalogger
 */
class DataLogBehavior extends Behavior
{
    /**
     * Разрешенные приложения
     * @var array
     */
    public $apps = ['app-backend'];

    /**
     * Предыдущие данные
     * @var null
     */
    private $_dataPrev;

    /**
     * Свойства для записи в лог
     * @var array
     */
    private $_attributes = [];

    /**
     * Подвязка к евентам
     * @return array
     */
    public function events() {
        return $this->_checkApp() ? [
            ActiveRecord::EVENT_AFTER_INSERT  => 'afterInsert',
            ActiveRecord::EVENT_AFTER_FIND    => 'afterFind',
            ActiveRecord::EVENT_AFTER_UPDATE  => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE  => 'afterDelete',
        ] : [];
    }

    /**
     * Инициализация
     */
    public function init() {
        if(!is_array($this->apps)){
            $this->apps = [$this->apps];
        }

        parent::init();
    }

    /**
     * Получаем старые атрибуты
     */
    public function afterFind() {
        $this->_dataPrev = $this->owner->attributes;
    }

    /**
     * Действие при создании
     */
    public function afterInsert() {
        $this->_attributes = array_merge($this->_attributes, [
            'type_key' => DataLog::TYPE_INSERT,
            'dataCurrent' => $this->owner->attributes,
            'dataPrev' => [],
        ]);
        $this->_saveData();
    }

    /**
     * Действие при обновлении
     */
    public function afterUpdate() {
        $this->_attributes = array_merge($this->_attributes, [
            'type_key' => DataLog::TYPE_UPDATE,
            'dataPrev' => $this->_dataPrev,
            'dataCurrent' => $this->owner->attributes,
        ]);
        $this->_saveData();
    }

    /**
     * Действие при удалении
     */
    public function afterDelete() {
        $this->_attributes = array_merge($this->_attributes,[
            'type_key' => DataLog::TYPE_DELETE,
            'dataPrev' => $this->owner->attributes,
            'dataCurrent' => [],
        ]);
        $this->_saveData();
    }

    /**
     * Сохранение лога
     */
    protected function _saveData() {
        $model = new DataLog();

        $model->setAttributes($this->_attributes);
        $model->model_name = $this->owner->className();
        $model->table_name = $this->owner->tableName();
        $model->recordId = $this->owner->getPrimaryKey(true);
        $model->save();
    }

    /**
     * Проверка приложения на доступ к записи лога
     * @return bool
     */
    protected function _checkApp()
    {
        if(!in_array(Yii::$app->id, $this->apps)) {
            return false;
        }

        return true;
    }
}