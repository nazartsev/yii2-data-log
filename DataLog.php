<?php

namespace platx\datalog;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;


/**
 * Логи записей в БД
 *
 * @property integer $id ID записи
 * @property string $model_name Название модели
 * @property string $table_name Название таблицы
 * @property string $type_key Ключ типа
 * @property string $type Тип
 * @property string $data_prev Предыдущие данные (Json)
 * @property string $data_current Текущие данные (Json)
 * @property array $dataPrev Предыдущие данные (Массив)
 * @property array $dataCurrent Текущие данные (Массив)
 * @property string $created_at Дата создания
 * @property string $app_id ID приложения
 * @property integer $user_id ID пользователя
 * @property string $record_id ID записи (Json)
 * @property array $recordId ID записи (Массив)
 */
class DataLog extends ActiveRecord
{
    /**
     * Константы типов
     */
    const TYPE_INSERT = 0;
    const TYPE_UPDATE = 1;
    const TYPE_DELETE = 2;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%data_log}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
	        [['model_name', 'type_key', 'data_current', 'created_at', 'record_id', 'table_name'], 'required'],
            [['id', 'user_id', 'type_key'], 'integer'],
            [['data_prev', 'data_current'], 'string'],
            [['dataPrev', 'dataCurrent', 'recordId'], 'safe'],
            [['model_name', 'table_name', 'app_id', 'record_id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        $this->app_id = Yii::$app->id;

        if(!empty(Yii::$app->user)) {
            $this->user_id = Yii::$app->user->id;
        }

        if(!$this->data_current) {
            $this->dataCurrent = [];
        }

        if(!$this->data_prev) {
            $this->dataPrev = [];
        }

        return parent::beforeValidate();
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'created_at',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_name' => 'Модель',
            'table_name' => 'Таблица',
            'type_key' => 'Тип',
            'type' => 'Тип',
            'data_prev' => 'Предыдущие данные',
            'data_current' => 'Текущие данные',
            'dataPrev' => 'Предыдущие данные',
            'dataCurrent' => 'Текущие данные',
            'created_at' => 'Дата создания',
            'user_id' => 'Пользователь',
            'record_id' => 'Запись',
            'recordId' => 'Запись',
            'app_id' => 'Приложение',
        ];
    }

    /**
     * @param null $key
     * @return array|mixed
     */
    public static function getTypes($key = null)
    {
        $items = [
            self::TYPE_INSERT => 'Создание',
            self::TYPE_UPDATE => 'Обновление',
            self::TYPE_DELETE => 'Удаление',
        ];

        return !is_null($key) ? ArrayHelper::getValue($items, $key) : $items;
    }

    /**
     * @return array|mixed
     */
    public function getType()
    {
        return self::getTypes($this->type_key);
    }

    /**
     * @param $newValue
     */
    public function setDataCurrent($newValue)
    {
        if(is_array($newValue)) {
            $this->data_current = Json::encode($newValue);
        }
    }

    /**
     * @return array|mixed
     */
    public function getDataCurrent()
    {
        return !empty($this->data_current) ? Json::decode($this->data_current) : [];
    }

    /**
     * @param $newValue
     */
    public function setDataPrev($newValue)
    {
        if(is_array($newValue)) {
            $this->data_prev = Json::encode($newValue);
        }
    }

    /**
     * @return array|mixed
     */
    public function getDataPrev()
    {
        return !empty($this->data_prev) ? Json::decode($this->data_prev) : [];
    }

    /**
     * @param $newValue
     */
    public function setRecordId($newValue)
    {
        if(is_array($newValue)) {
            $this->record_id = Json::encode($newValue);
        } else {
            $this->record_id = $newValue;
        }
    }

    /**
     * @return array|mixed
     */
    public function getRecordId()
    {
        $recordId = Json::decode($this->record_id);

        if($recordId) {
            return $recordId;
        }

        return $this->record_id;
    }
}
