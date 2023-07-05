## Общее
### Хедеры

`Accept` => `application/json`

`Content-Type` => `application/json`

`Authorization` => `Bearer %TOKEN%`

`TOKEN` - это значение `tcrm_frx_tenants.api_token` на борде.

### Ошибки валидации

HTTP код 422, подробное описание ошибок в теле запроса.

Пример:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "call_at": [
            "The call at must be a date after 2021-04-06 16:15:59."
        ]
    }
}
```



## Обновить лид

Endpoint PUT `/frx/leads/{lead_id}`

`lead_id` - внешний(tcrm) id лида.

### Параметры:

`status` - **string**, не обязателен, статус лида. Может быть: 
`Новый`, `Отказ`, `В работе у клоузера`, `Нет ответа`, `Дозвонится`, 
`Демонстрация`, `Депозит`, `Перезвон`, `Дубль`, `Неверный номер`, `Резерв`.

### Успешный ответ

HTTP код 202, в теле ответа пусто.



## Создать колбек
***Если на лиде есть не завершенный колбек, то обновит его***

Endpoint POST `/frx/leads/{lead_id}/callbacks`

`lead_id` - внешний(tcrm) id лида.

### Параметры:

`call_at` - **timestamp**, обязателен, дата и время коллбека

### Успешный ответ

HTTP код 202, в теле ответа пусто.



## Отметить колбек набранным

Endpoint POST `/frx/leads/{lead_id}/callbacks/mark-called`

`lead_id` - внешний(tcrm) id лида.

### Параметры:

`called_at` - **timestamp**, не обязателен, дата и время звонка. По умолчанию - `now()`

`call_id` - **string**, не обязателен, внешний(tcrm) id звонка.

### Успешный ответ

HTTP код 202, в теле ответа пусто.
