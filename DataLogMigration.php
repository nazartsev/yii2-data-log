<?php

namespace platx\datalog;

use yii\db\Migration;


/**
 * Создание таблицы для логов
 * @package platx\datalog
 */
class DataLogMigration extends Migration
{
    /**
     * @var string
     */
    protected $_tableName = '{{%data_log}}';

    /**
     * Накат миграции
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable($this->_tableName, [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->integer(),
            'app_id' => $this->string(),
            'record_id' => $this->string(),
            'model_name' => $this->string(),
            'table_name' => $this->string()->notNull(),
            'data_prev' => $this->text(),
            'data_current' => $this->text()->notNull(),
            'type_key' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime(),
        ], $tableOptions);
    }

    /**
     * Откат миграции
     */
    public function safeDown()
    {
        $this->dropTable($this->_tableName);
    }
}