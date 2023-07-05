<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'common_error'                   => 'Ошибка валидации данных. Проверьте информацию, и попробуйте ещё раз.',
    'status_deposit_blocked'         => 'Редактирование статуса данного лида заблокировано, обратитесь, пожалуйста к поддержке',
    'all_assignments_in_one_office'  => 'Все лиды должны быть в одном одном офисе',
    'assignment_is_already_leftover' => 'Назначение уже было помечено как LO.',

    'accepted'        => 'Поле :attribute должно быть принято.',
    'active_url'      => 'Поле :attribute содержит невалидный URL.',
    'after'           => 'Значение :attribute должно быть датой позднее чем :date.',
    'after_or_equal'  => 'Значение :attribute должно быть датой большей или равной :date.',
    'alpha'           => 'Значение :attribute должно содержать только буквы.',
    'alpha_dash'      => 'Значение :attribute должно содержать только буквы, числа, тире и подчеркивания.',
    'alpha_num'       => 'Значение :attribute должно содержать только буквы и числа.',
    'array'           => 'Значение :attribute должно быть массивом.',
    'before'          => 'Значение :attribute должно быть датой предшествующей :date.',
    'before_or_equal' => 'Значение :attribute должно быть датой предшествующей или равной :date.',
    'between'         => [
        'numeric' => 'Значение :attribute должно быть между :min и :max.',
        'file'    => 'Файл :attribute должен быть между :min и :max килобайт.',
        'string'  => 'Значение :attribute должно быть между :min и :max символов.',
        'array'   => 'Количество элементов в :attribute должно быть между :min и :max.',
    ],
    'boolean'        => 'Значение :attribute должно true или false.',
    'confirmed'      => 'Значение :attribute подтверждения не совпадает.',
    'date'           => 'Значение :attribute невалидная дата.',
    'date_equals'    => 'Значение :attribute должно быть датой равной :date.',
    'date_format'    => 'Значение :attribute не соответствует формату :format.',
    'different'      => 'Значения :attribute и :other должны быть разными.',
    'digits'         => 'Значение :attribute должно содержать :digits цифр.',
    'digits_between' => 'Значение :attribute должно быть между :min и :max цифр.',
    'dimensions'     => 'Поле :attribute имеет не верные размеры.',
    'distinct'       => 'Значение :attribute поля имеет дублирующиеся значение.',
    'email'          => 'Значение :attribute должно быть валидным e-mail адресом.',
    'ends_with'      => 'Значение :attribute должно заканчиваться одним и следующих: :values',
    'exists'         => 'Выбрано некорректное значение для :attribute.',
    'file'           => 'Поле :attribute должно содержать файлом.',
    'filled'         => 'Поле :attribute должно иметь какоето значение.',
    'gt'             => [
        'numeric' => 'Значение :attribute должно быть больше чем :value.',
        'file'    => 'Файл :attribute должен быть больше чем :value килобайт.',
        'string'  => 'Значение :attribute должно содержать больше чем :value символов.',
        'array'   => 'Количество элементов в :attribute должно быть больше чем :value.',
    ],
    'gte' => [
        'numeric' => 'Значение :attribute должно быть больше чем или равным :value.',
        'file'    => 'Файл :attribute должен быть больше чем или равным :value калобайтам.',
        'string'  => 'Значение :attribute должно содержать :value или больше символов.',
        'array'   => 'Количество элементов в :attribute должно быть :value или больше.',
    ],
    'image'    => 'Поле :attribute должно быть картинкой.',
    'in'       => 'Выбрано некорректное значение для :attribute.',
    'in_array' => 'Значение :attribute поля нету в :other.',
    'integer'  => 'Поле :attribute должно быть целым числом.',
    'ip'       => 'Значение :attribute должно быть валидным IP адресом.',
    'ipv4'     => 'Значение :attribute должно быть валидным IPv4 адресом.',
    'ipv6'     => 'Значение :attribute должно быть валидным IPv6 адресом.',
    'json'     => 'Значение :attribute должно быть валидной JSON строкой.',
    'lt'       => [
        'numeric' => 'Значение :attribute должно быть меньше чем :value.',
        'file'    => 'Файл :attribute должно быть меньше чем :value килобайт.',
        'string'  => 'Значение :attribute должно содержать меньше чем :value символов.',
        'array'   => 'Количество элементов в :attribute должно быть меньше :value.',
    ],
    'lte' => [
        'numeric' => 'Значение :attribute должно быть меньшим или равным :value.',
        'file'    => 'Файл :attribute должно быть меньшим или равным :value килобайт.',
        'string'  => 'Значение :attribute должно содержать меньше чем :value символов.',
        'array'   => 'Количество элементов в :attribute должно быть :value или меньше.',
    ],
    'max' => [
        'numeric' => 'Значение :attribute не должно превышать :max.',
        'file'    => 'Файл :attribute не должен быть больше :max килобайт.',
        'string'  => 'Поле :attribute не должно превышать :max символов.',
        'array'   => 'Количество элементов в :attribute не должно быть больше :max.',
    ],
    'mimes'     => 'Поле :attribute должно быть файлом типа: :values.',
    'mimetypes' => 'Поле :attribute должно быть файлом типа: :values.',
    'min'       => [
        'numeric' => 'Значение :attribute должно быть минимум :min.',
        'file'    => 'Файл :attribute должен быть :min килобайт.',
        'string'  => 'Поле :attribute должно быть минимум :min символа.',
        'array'   => 'Количество элементов в :attribute должно быть минимум :min.',
    ],
    'not_in'               => 'Выбранное значение :attribute невалидно.',
    'not_regex'            => 'Формат значения :attribute невалиден.',
    'numeric'              => 'Поле :attribute должно быть числом.',
    'password'             => 'Пароль некорректен.',
    'present'              => 'Поле :attribute должно присутствовать.',
    'regex'                => 'Формат значения :attribute невалиден.',
    'required'             => 'Поле :attribute обязательно.',
    'required_if'          => 'Поле :attribute обязательно когда :other равно :value.',
    'required_unless'      => 'Поле :attribute обязательно когда :other не в :values.',
    'required_with'        => 'Поле :attribute обязательно когда одно из :values присутствует.',
    'required_with_all'    => 'Поле :attribute обязательно когда все :values присутствуют.',
    'required_without'     => 'Поле :attribute обязательно когда одно из :values отсутствует.',
    'required_without_all' => 'Поле :attribute обязательно когда все :values отсутствуют.',
    'same'                 => 'Значения :attribute и :other должны совпадать.',
    'size'                 => [
        'numeric' => 'Значение :attribute должно быть :size.',
        'file'    => 'Файл :attribute должен быть :size килобайт.',
        'string'  => 'Значение :attribute должно быть :size символов.',
        'array'   => 'Количество элементов в :attribute должно быть :size.',
    ],
    'starts_with' => 'Значение :attribute должно начинаться с одного из: :values',
    'string'      => 'Поле :attribute должно быть строкой.',
    'timezone'    => 'Значение :attribute валидным часовым поясом.',
    'unique'      => ':attribute уже существует.',
    'uploaded'    => ':attribute не удалось загрузить.',
    'url'         => 'Значение :attribute имеет невалидный URL формат.',
    'uuid'        => 'Значение :attribute должно быть валидным UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'manager_id'    => 'Менеджер',
        'status'        => 'Статус',
        'reject_reason' => 'Причина отказа',
        'deposit_sum'   => 'Сумма депозита',
        'gender_id'     => 'Пол',
        'age'           => 'Возраст',
        'profession'    => 'Профессия',
        'comment'       => 'Комментарий',
        'alt_phone'     => 'Альтернативный номер',
        'callback_at'   => 'Дата и время перезвона',
    ],

];
