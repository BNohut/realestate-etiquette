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

    'accepted' => ':attribute kabul edilmelidir.',
    'accepted_if' => ':attribute, :other :value olduğunda kabul edilmelidir.',
    'active_url' => ':attribute geçerli bir URL değil.',
    'after' => ':attribute, :date tarihinden sonra olmalıdır.',
    'after_or_equal' => ':attribute, :date tarihinden sonra veya aynı tarihte olmalıdır.',
    'alpha' => ':attribute sadece harfler içermelidir.',
    'alpha_dash' => ':attribute sadece harfler, rakamlar, tireler ve alt çizgiler içermelidir.',
    'alpha_num' => ':attribute sadece harfler ve rakamlar içermelidir.',
    'array' => ':attribute bir dizi olmalıdır.',
    'ascii' => ':attribute yalnızca tek baytlık alfanumerik karakterler ve semboller içermelidir.',
    'before' => ':attribute, :date tarihinden önce olmalıdır.',
    'before_or_equal' => ':attribute, :date tarihinden önce veya aynı tarihte olmalıdır.',
    'between' => [
        'array' => ':attribute, en az :min en çok :max öğe içermelidir.',
        'file' => ':attribute, en az :min en çok :max kilobayt olmalıdır.',
        'numeric' => ':attribute, en az :min en çok :max arasında olmalıdır.',
        'string' => ':attribute, en az :min en çok :max karakter içermelidir.',
    ],

    'boolean' => ':attribute alanı true veya false olmalıdır.',
'confirmed' => ':attribute doğrulaması eşleşmiyor.',
'current_password' => 'Şifre yanlış.',
'date' => ':attribute geçerli bir tarih değil.',
'date_equals' => ':attribute tarihi :date tarihine eşit olmalıdır.',
'date_format' => ':attribute biçimi :format ile eşleşmiyor.',
'decimal' => ':attribute :decimal ondalık basamağa sahip olmalıdır.',
'declined' => ':attribute reddedilmelidir.',
'declined_if' => ':other :value olduğunda :attribute reddedilmelidir.',
'different' => ':attribute ve :other farklı olmalıdır.',
'digits' => ':attribute :digits rakam olmalıdır.',
'digits_between' => ':attribute :min ile :max rakam arasında olmalıdır.',
'dimensions' => ':attribute geçersiz resim boyutlarına sahip.',
'distinct' => ':attribute alanı yinelenen bir değere sahip.',
'doesnt_end_with' => ':attribute aşağıdakilerden biriyle bitmemeli: :values.',
'doesnt_start_with' => ':attribute aşağıdakilerden biriyle başlamamalı: :values.',
'email' => ':attribute geçerli bir e-posta adresi olmalıdır.',
'ends_with' => ':attribute aşağıdakilerden biriyle bitmelidir: :values.',
'enum' => 'Seçilen :attribute geçersiz.',
'exists' => 'Seçilen :attribute geçersiz.',
'file' => ':attribute bir dosya olmalıdır.',
'filled' => ':attribute alanı bir değer içermelidir.',
'gt' => [
    'array' => ':attribute :value öğeden daha fazla öğe içermelidir.',
    'file' => ':attribute :value kilobayttan daha büyük olmalıdır.',
    'numeric' => ':attribute :value\'dan daha büyük olmalıdır.',
    'string' => ':attribute :value karakterden daha uzun olmalıdır.',
],

    'gte' => [
        'array' => ' :attribute :value veya daha fazla öğe içermelidir.',
        'file' => ' :attribute :value kilobayttan büyük veya eşit olmalıdır.',
        'numeric' => ' :attribute :value veya daha büyük olmalıdır.',
        'string' => ' :attribute :value karakterden uzun veya eşit olmalıdır.',
    ],
    'image' => ' :attribute bir resim olmalıdır.',
    'in' => 'Seçilen :attribute geçersizdir.',
    'in_array' => ' :attribute alanı :other içinde mevcut değil.',
    'integer' => ' :attribute bir tam sayı olmalıdır.',
    'ip' => ' :attribute geçerli bir IP adresi olmalıdır.',
    'ipv4' => ' :attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6' => ' :attribute geçerli bir IPv6 adresi olmalıdır.',
    'json' => ' :attribute geçerli bir JSON dizgesi olmalıdır.',
    'lowercase' => ' :attribute küçük harf olmalıdır.',
    'lt' => [
        'array' => ' :attribute :value öğeden az olmalıdır.',
        'file' => ' :attribute :value kilobayttan küçük olmalıdır.',
        'numeric' => ' :attribute :value\'dan küçük olmalıdır.',
        'string' => ' :attribute :value karakterden kısa olmalıdır.',
    ],
    'lte' => [
        'array' => ' :attribute :value öğeden fazla olmamalıdır.',
        'file' => ' :attribute :value kilobayttan küçük veya eşit olmalıdır.',
        'numeric' => ' :attribute :value veya daha az olmalıdır.',
        'string' => ' :attribute :value karakterden kısa veya eşit olmalıdır.',
    ],
    'mac_address' => ' :attribute geçerli bir MAC adresi olmalıdır.',
    'max' => [
        'array' => ' :attribute en fazla :max öğe içerebilir.',
        'file' => ' :attribute :max kilobayttan büyük olamaz.',
        'numeric' => ' :attribute :max\'dan büyük olamaz.',
        'string' => ' :attribute :max karakterden uzun olamaz.',
    ],
    'max_digits' => ' :attribute :max basamaktan fazla olmamalıdır.',
    'mimes' => ' :attribute dosya türü :values olmalıdır.',
    'mimetypes' => ' :attribute dosya türü :values olmalıdır.',
    'min' => [
        'array' => ':attribute en az :min öğe içermelidir.',
        'file' => ':attribute en az :min kilobayt olmalıdır.',
        'numeric' => ':attribute en az :min olmalıdır.',
        'string' => ':attribute en az :min karakter olmalıdır.',
    ],
    'min_digits' => ':attribute en az :min rakam içermelidir.',
    'missing' => ':attribute alanı eksik olmalıdır.',
    'missing_if' => ':attribute alanı, :other :value olduğunda eksik olmalıdır.',
    'missing_unless' => ':attribute alanı, :other :value olmadığında eksik olmalıdır.',
    'missing_with' => ':values mevcut olduğunda :attribute alanı eksik olmalıdır.',
    'missing_with_all' => ':values mevcut olduğunda :attribute alanı eksik olmalıdır.',
    'multiple_of' => ':attribute, :value katı olmalıdır.',
    'not_in' => 'Seçilen :attribute geçersiz.',
    'not_regex' => ':attribute formatı geçersiz.',
    'numeric' => ':attribute bir sayı olmalıdır.',
    'password' => [
        'letters' => ':attribute en az bir harf içermelidir.',
        'mixed' => ':attribute en az bir büyük harf ve bir küçük harf içermelidir.',
        'numbers' => ':attribute en az bir sayı içermelidir.',
        'symbols' => ':attribute en az bir sembol içermelidir.',
        'uncompromised' => 'Verilen :attribute bir veri sızıntısında göründü. Lütfen farklı bir :attribute seçin.',
    ],
    'present' => 'The :attribute alanı mevcut olmalıdır.',
    'prohibited' => 'The :attribute alanı yasaktır.',
    'prohibited_if' => 'The :attribute alanı, :other değeri :value olduğunda yasaktır.',
    'prohibited_unless' => 'The :attribute alanı, :other değeri :values içinde olmadıkça yasaktır.',
    'prohibits' => ':attribute alanı, :other alanının mevcut olmasını engeller.',
    'regex' => ':attribute formatı geçersiz.',
    'required' => ':attribute alanı gereklidir.',
    'required_array_keys' => ':attribute alanı için şunlar için girişler içermelidir: :values.',
    'required_if' => ':other değeri :value olduğunda :attribute alanı gereklidir.',
    'required_if_accepted' => ':other kabul edildiğinde :attribute alanı gereklidir.',
    'required_unless' => ':other :values içinde olmadıkça :attribute alanı gereklidir.',
    'required_with' => ':values mevcut olduğunda :attribute alanı gereklidir.',
    'required_with_all' => ':values mevcut olduğunda :attribute alanı gereklidir.',
    'required_without' => ':values mevcut olmadığında :attribute alanı gereklidir.',
    'required_without_all' => ':values hiçbiri mevcut olmadığında :attribute alanı gereklidir.',
    'same' => ':attribute ve :other eşleşmelidir.',
    'size' => [
        'array' => ':attribute :size öğe içermelidir.',
        'file' => ':attribute :size kilobayt olmalıdır.',
        'numeric' => ':attribute :size olmalıdır.',
        'string' => ':attribute :size karakter olmalıdır.',
    ],
    'starts_with' => ':attribute şu değerlerden biriyle başlamalıdır: :values.',
    'string' => ':attribute bir dize olmalıdır.',
    'timezone' => ':attribute geçerli bir saat dilimi olmalıdır.',
    'unique' => ':attribute zaten alınmış.',
    'uploaded' => ':attribute yüklenemedi.',
    'uppercase' => ':attribute büyük harf olmalıdır.',
    'url' => ':attribute geçerli bir URL olmalıdır.',
    'ulid' => ':attribute geçerli bir ULID olmalıdır.',
    'uuid' => ':attribute geçerli bir UUID olmalıdır.',

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

    'attributes' => [],

];
