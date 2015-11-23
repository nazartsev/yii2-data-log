Yii2 Data Log
================
Логирование изменений в базе с возможностью последующего отката на предыдущее состояние.

Установка
------------

Лучший способ установить - через [composer](http://getcomposer.org/download/).

Запустить

```
php composer.phar require --prefer-dist platx/yii2-data-logger "*"
```

либо добавить

```
"platx/yii2-data-logger": "*"
```

в секцию require в файле `composer.json` Вашего приложения.


Использование
-----

После установки нужно создать и накатить миграцию на создание таблицы для логов:

```php
class m151123_135616_create_table_data_log extends \platx\datalog\DataLogMigration{}
```

Дальше нужно подключить поведение к моделям, которые могут учавствовать в логировании:

```php
public function behaviors()
{
    return [
        'data-log' => [
            'class' => 'platx\datalog\DataLogBehavior',
            'apps' => ['app-backend', 'app-frontend'],
        ]
        ...
    ];
}
```

где `apps` - _массив_ с ID разрешенных приложений, которые будут логироваться, по умолчанию - `app-backend`.

Логирование настроено. Дополнительным функционалом является откат изменений на предыдущее состояние, 
для этого можно воспользоваться следующими екшенами в нужном контроллере: 

```php
public function actions()
{
    return [
        'index' => [
            'class' => 'platx\datalog\actions\IndexAction',
            'viewFile' => 'index',
            'pageSize' => 20
        ],
        'view' => [
            'class' => 'platx\datalog\actions\ViewAction',
            'viewFile' => 'view'
        ],
        'rollback' => [
            'class' => 'platx\datalog\actions\RollbackAction',
            'redirectUrl' => ['index']
        ],
        'delete' => [
            'class' => 'platx\datalog\actions\DeleteAction',
            'redirectUrl' => ['index']
        ],
    ];
}
```

где `viewFile` - представление, куда выводить данные. `pageSize` - количество записей на 
страницу, `redirectUrl` - куда перенаправлять.

IndexAction в представление передает $dataProvider, ViewAction - $model.