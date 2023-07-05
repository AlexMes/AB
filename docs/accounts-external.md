Endpoint GET `/api/external/accounts`

### Хедеры

`Accept` => `application/json`
`Content-Type` => `application/json`
`Authorization` => `Bearer %TOKEN HERE%`

### Параметры:

`search` - не обязателен, **string**, поиск по имени или id аккаунта
`orderBy` - не обязателен, **string**, сортировка по полю, возможные значения: `spend`, `lifetime`. По умолчанию `spend`.
`dir` - не обязателен, **string**, сортировать по возрастанию или убыванию, значения: `asc`, `desc`. По умолчанию `desc`.
`perPage` - не обязателен, **integer**, указывает по сколько записей отображать за раз. По умолчанию `100`.
`since` - начальная дата фильтра по периоду. Формат `YYYY-MM-DD`. По умолчанию - сегодня
`until` - конечная дата фильтра по периоду. Формат `YYYY-MM-DD`. По умолчанию - сегодня

### Пример ответа:

```
{
    "data": [
        {
            "id": "act_247980826501033",
            "account_id": "247980826501033",
            "name": "Wear auto",
            "spend": "0",
            "lifetime": 5731476,
            "is_banned": false
        }
    ],
    "links": {
        "first": "https://ads-board.app/api/external/accounts?page=1",
        "last": "https://ads-board.app/api/external/accounts?page=17009",
        "prev": null,
        "next": "https://ads-board.app/api/external/accounts?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 17009,
        "path": "https://ads-board.app/api/external/accounts",
        "per_page": "1",
        "to": 1,
        "total": 17009
    }
}
```
